<?php
/**
 * Download link verification.
 *
 * Results are stored in the transient `zabisoft_link_{md5}` so that the
 * ZabiSoft theme (and any other theme) can read them without performing a
 * remote request while rendering a page.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks that download URLs are reachable.
 */
class ZSM_Link_Checker {

	/**
	 * Cache lifetime in seconds.
	 *
	 * @return int
	 */
	public function cache_ttl() {
		return (int) apply_filters( 'zsm_link_cache_ttl', HOUR_IN_SECONDS );
	}

	/**
	 * Transient key for a URL.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function cache_key( $url ) {
		return 'zabisoft_link_' . md5( (string) $url );
	}

	/**
	 * Read the cached status without any network access.
	 *
	 * @param string $url URL.
	 * @return array
	 */
	public function cached_status( $url ) {
		$cached = get_transient( self::cache_key( $url ) );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		return array(
			'ok'     => true,
			'reason' => 'unchecked',
			'code'   => 0,
			'time'   => 0,
		);
	}

	/**
	 * Perform the check (HEAD request, then a ranged GET fallback).
	 *
	 * @param string $url   URL.
	 * @param bool   $force Ignore the cache.
	 * @return array
	 */
	public function check( $url, $force = false ) {
		$url = esc_url_raw( trim( (string) $url ) );

		$result = array(
			'ok'     => false,
			'reason' => 'invalid',
			'code'   => 0,
			'time'   => time(),
		);

		if ( '' === $url || ! wp_http_validate_url( $url ) ) {
			return $result;
		}

		$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );

		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
			$result['reason'] = 'scheme';

			return $result;
		}

		if ( ! $force ) {
			$cached = get_transient( self::cache_key( $url ) );

			if ( is_array( $cached ) ) {
				return $cached;
			}
		}

		$args = array(
			'timeout'     => 8,
			'redirection' => 3,
			'user-agent'  => 'ZabiSoft/' . ZSM_VERSION . ' link checker (+' . home_url( '/' ) . ')',
			'sslverify'   => true,
		);

		$response = wp_remote_head( $url, $args );

		if ( is_wp_error( $response ) || in_array( (int) wp_remote_retrieve_response_code( $response ), array( 403, 405, 501 ), true ) ) {
			$args['headers'] = array( 'Range' => 'bytes=0-1024' );
			$response        = wp_remote_get( $url, $args );
		}

		if ( is_wp_error( $response ) ) {
			$result['reason'] = 'unreachable';
			$result['detail'] = $response->get_error_message();
		} else {
			$code             = (int) wp_remote_retrieve_response_code( $response );
			$result['code']   = $code;
			$result['ok']     = ( $code > 0 && $code < 400 );
			$result['reason'] = $result['ok'] ? 'ok' : 'http-' . $code;
		}

		set_transient( self::cache_key( $url ), $result, $this->cache_ttl() );

		return $result;
	}

	/**
	 * Check every mirror of one entry.
	 *
	 * @param int  $post_id Post ID.
	 * @param bool $force   Ignore the cache.
	 * @return array Map of url => result.
	 */
	public function check_post( $post_id, $force = false ) {
		$raw     = get_post_meta( (int) $post_id, '_zs_mirrors', true );
		$results = array();

		if ( ! is_array( $raw ) ) {
			return $results;
		}

		foreach ( $raw as $mirror ) {
			if ( empty( $mirror['url'] ) ) {
				continue;
			}

			$results[ $mirror['url'] ] = $this->check( $mirror['url'], $force );
		}

		return $results;
	}

	/**
	 * Hook the AJAX endpoint used by the “Check link” buttons.
	 *
	 * @return void
	 */
	public static function ajax_check() {
		check_ajax_referer( 'zsm_admin', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to check links.', 'zabisoft-software-manager' ) ), 403 );
		}

		$url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';

		if ( '' === $url ) {
			wp_send_json_error( array( 'message' => __( 'No URL submitted.', 'zabisoft-software-manager' ) ) );
		}

		$checker = new self();
		$result  = $checker->check( $url, true );

		wp_send_json_success(
			array(
				'ok'      => (bool) $result['ok'],
				'code'    => (int) $result['code'],
				'reason'  => (string) $result['reason'],
				'message' => $result['ok']
					? sprintf(
						/* translators: %d: HTTP status code. */
						__( 'Reachable (HTTP %d).', 'zabisoft-software-manager' ),
						(int) $result['code']
					)
					: sprintf(
						/* translators: 1: reason, 2: HTTP status code. */
						__( 'Problem detected (%1$s, code %2$d).', 'zabisoft-software-manager' ),
						(string) $result['reason'],
						(int) $result['code']
					),
			)
		);
	}
}
