<?php
/**
 * Software data helpers.
 *
 * The theme never requires the ZabiSoft Software Manager plugin, but when the
 * plugin is active it owns the data. These helpers read the shared meta keys so
 * that templates work in both cases.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field map: meta key => human label used in the specs table.
 *
 * @return array
 */
function zabisoft_software_fields() {
	return array(
		'_zs_version'       => __( 'Version', 'zabisoft' ),
		'_zs_file_size'     => __( 'File size', 'zabisoft' ),
		'_zs_setup_file'    => __( 'Setup file name', 'zabisoft' ),
		'_zs_setup_type'    => __( 'Setup type', 'zabisoft' ),
		'_zs_compatibility' => __( 'Compatibility', 'zabisoft' ),
		'_zs_license'       => __( 'Licence', 'zabisoft' ),
		'_zs_release_date'  => __( 'Last update', 'zabisoft' ),
		'_zs_developer'     => __( 'Developer', 'zabisoft' ),
		'_zs_website'       => __( 'Official website', 'zabisoft' ),
		'_zs_languages'     => __( 'Interface language', 'zabisoft' ),
	);
}

/**
 * Requirement fields: meta key => label.
 *
 * @return array
 */
function zabisoft_requirement_fields() {
	return array(
		'_zs_req_os'    => __( 'Operating system', 'zabisoft' ),
		'_zs_req_cpu'   => __( 'Processor', 'zabisoft' ),
		'_zs_req_ram'   => __( 'Memory (RAM)', 'zabisoft' ),
		'_zs_req_disk'  => __( 'Hard disk space', 'zabisoft' ),
		'_zs_req_gpu'   => __( 'Graphics card', 'zabisoft' ),
		'_zs_req_other' => __( 'Other requirements', 'zabisoft' ),
	);
}

/**
 * Read all software meta for a post.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function zabisoft_get_software_meta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$data    = array();

	foreach ( array_keys( zabisoft_software_fields() ) as $key ) {
		$data[ $key ] = (string) get_post_meta( $post_id, $key, true );
	}

	foreach ( array_keys( zabisoft_requirement_fields() ) as $key ) {
		$data[ $key ] = (string) get_post_meta( $post_id, $key, true );
	}

	$data['_zs_features']      = (string) get_post_meta( $post_id, '_zs_features', true );
	$data['_zs_install_steps'] = (string) get_post_meta( $post_id, '_zs_install_steps', true );
	$data['_zs_screenshots']   = (array) get_post_meta( $post_id, '_zs_screenshots', true );
	$data['_zs_mirrors']       = zabisoft_get_mirrors( $post_id );
	$data['_zs_countdown']     = (int) get_post_meta( $post_id, '_zs_countdown', true );

	return $data;
}

/**
 * Normalise the download mirrors list.
 *
 * Mirrors are stored as an array of arrays with the keys label, url, size, type,
 * note and platform. Anything unexpected is filtered out.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function zabisoft_get_mirrors( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$raw     = get_post_meta( $post_id, '_zs_mirrors', true );

	if ( is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
		$raw     = is_array( $decoded ) ? $decoded : array();
	}

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$mirrors = array();

	foreach ( $raw as $mirror ) {
		if ( ! is_array( $mirror ) ) {
			continue;
		}

		$url = isset( $mirror['url'] ) ? esc_url_raw( trim( (string) $mirror['url'] ) ) : '';

		if ( '' === $url || ! wp_http_validate_url( $url ) ) {
			continue;
		}

		$mirrors[] = array(
			'label'    => isset( $mirror['label'] ) ? sanitize_text_field( $mirror['label'] ) : __( 'Download', 'zabisoft' ),
			'url'      => $url,
			'size'     => isset( $mirror['size'] ) ? sanitize_text_field( $mirror['size'] ) : '',
			'type'     => isset( $mirror['type'] ) ? sanitize_text_field( $mirror['type'] ) : '',
			'note'     => isset( $mirror['note'] ) ? sanitize_text_field( $mirror['note'] ) : '',
			'platform' => isset( $mirror['platform'] ) ? sanitize_text_field( $mirror['platform'] ) : '',
		);
	}

	return $mirrors;
}

/**
 * Split a textarea value into a clean list (one item per line).
 *
 * @param string $value Raw textarea.
 * @return array
 */
function zabisoft_lines_to_list( $value ) {
	$value = (string) $value;

	if ( '' === trim( $value ) ) {
		return array();
	}

	$lines = preg_split( '/\r\n|\r|\n/', $value );
	$out   = array();

	foreach ( $lines as $line ) {
		$line = trim( wp_strip_all_tags( $line ) );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}

	return $out;
}

/**
 * Build the rows shown in the “Technical setup details” table.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function zabisoft_specs_rows( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$rows    = array();

	foreach ( zabisoft_software_fields() as $key => $label ) {
		$value = (string) get_post_meta( $post_id, $key, true );

		if ( '' === $value ) {
			continue;
		}

		if ( '_zs_release_date' === $key ) {
			$timestamp = strtotime( $value );
			if ( $timestamp ) {
				$value = date_i18n( get_option( 'date_format' ), $timestamp );
			}
		}

		$rows[ $label ] = $value;
	}

	$rows[ __( 'Added on', 'zabisoft' ) ] = get_the_date( '', $post_id );

	return $rows;
}

/**
 * Build the system requirements rows.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function zabisoft_requirement_rows( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$rows    = array();

	foreach ( zabisoft_requirement_fields() as $key => $label ) {
		$value = (string) get_post_meta( $post_id, $key, true );

		if ( '' !== $value ) {
			$rows[ $label ] = $value;
		}
	}

	return $rows;
}

/**
 * Whether a post has any software specific information.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function zabisoft_has_software_data( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( zabisoft_get_mirrors( $post_id ) ) {
		return true;
	}

	if ( zabisoft_specs_rows( $post_id ) ) {
		return true;
	}

	if ( '' !== (string) get_post_meta( $post_id, '_zs_features', true ) ) {
		return true;
	}

	return false;
}

/**
 * Human readable host for an external URL, used to disclose the destination.
 *
 * @param string $url URL.
 * @return string
 */
function zabisoft_url_host( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( ! $host ) {
		return '';
	}

	return strtolower( preg_replace( '/^www\./i', '', $host ) );
}

/**
 * Check an external download URL and cache the result.
 *
 * Only the administrator triggered checks (on save, from the admin list, or by
 * the optional WP-Cron job) perform a request; front end rendering reuses the
 * cached value so page speed is never affected by a remote call.
 *
 * @param string $url        URL to check.
 * @param bool   $force      Force a fresh request.
 * @return array{ok:bool,reason:string,code:int,time:int}
 */
function zabisoft_check_url( $url, $force = false ) {
	$result = array(
		'ok'     => false,
		'reason' => 'invalid',
		'code'   => 0,
		'time'   => time(),
	);

	if ( ! is_string( $url ) || '' === $url || ! wp_http_validate_url( $url ) ) {
		return $result;
	}

	$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );

	if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
		$result['reason'] = 'scheme';

		return $result;
	}

	$host      = zabisoft_url_host( $url );
	$home_host = zabisoft_url_host( home_url( '/' ) );

	// Internal links always resolve.
	if ( $host && $home_host && $host === $home_host ) {
		return array(
			'ok'     => true,
			'reason' => 'internal',
			'code'   => 200,
			'time'   => time(),
		);
	}

	$cache_key = 'zabisoft_link_' . md5( $url );
	$cached    = get_transient( $cache_key );

	if ( ! $force && is_array( $cached ) ) {
		return $cached;
	}

	if ( ! apply_filters( 'zabisoft_allow_link_check', true, $url ) ) {
		return $result;
	}

	$response = wp_remote_head(
		$url,
		array(
			'timeout'     => 7,
			'redirection' => 3,
			'user-agent'  => 'ZabiSoft/' . ZABISOFT_VERSION . ' (link checker)',
			'sslverify'   => true,
		)
	);

	if ( is_wp_error( $response ) ) {
		// Some servers reject HEAD requests; retry with a ranged GET.
		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => 7,
				'redirection' => 3,
				'user-agent'  => 'ZabiSoft/' . ZABISOFT_VERSION . ' (link checker)',
				'sslverify'   => true,
				'headers'     => array( 'Range' => 'bytes=0-512' ),
			)
		);
	}

	if ( is_wp_error( $response ) ) {
		$result['reason'] = 'unreachable';
		set_transient( $cache_key, $result, HOUR_IN_SECONDS );

		return $result;
	}

	$code            = (int) wp_remote_retrieve_response_code( $response );
	$result['code']  = $code;
	$result['ok']    = ( $code > 0 && $code < 400 );
	$result['reason'] = $result['ok'] ? 'ok' : 'http-' . $code;

	set_transient( $cache_key, $result, HOUR_IN_SECONDS );

	return $result;
}

/**
 * Cached status for a mirror URL (never performs a request).
 *
 * @param string $url URL.
 * @return array
 */
function zabisoft_cached_link_status( $url ) {
	$cached = get_transient( 'zabisoft_link_' . md5( $url ) );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	if ( zabisoft_url_host( $url ) === zabisoft_url_host( home_url( '/' ) ) ) {
		return array(
			'ok'     => true,
			'reason' => 'internal',
			'code'   => 200,
			'time'   => 0,
		);
	}

	return array(
		'ok'     => true,
		'reason' => 'unchecked',
		'code'   => 0,
		'time'   => 0,
	);
}

/**
 * Add stable ids to the headings found in the post content and collect them
 * into a table of contents list.
 *
 * @param string $content Raw post content.
 * @return array{content:string,items:array<int,array{id:string,text:string,level:int}>}
 */
function zabisoft_build_toc( $content ) {
	$items = array();
	$index = 0;

	$content = preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		function ( $matches ) use ( &$items, &$index ) {
			$level = (int) $matches[1];
			$attrs = $matches[2];
			$text  = wp_strip_all_tags( $matches[3] );
			$text  = trim( preg_replace( '/\s+/', ' ', $text ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			if ( preg_match( '/id=("|\')([^"\']+)\1/i', $attrs, $id_match ) ) {
				$id = $id_match[2];
			} else {
				$index++;
				$id     = 'zs-section-' . $index;
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}

			$items[] = array(
				'id'    => $id,
				'text'  => $text,
				'level' => $level,
			);

			return '<h' . $level . $attrs . '>' . $matches[3] . '</h' . $level . '>';
		},
		(string) $content
	);

	return array(
		'content' => $content,
		'items'   => $items,
	);
}

/**
 * Front page listing: merge software entries and blog posts when the front
 * page shows the latest posts.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function zabisoft_front_page_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() ) {
		return;
	}

	$mode = zabisoft_mod( 'front_posts_option' );

	if ( 'posts' === $mode ) {
		return;
	}

	if ( 'software' === $mode && post_type_exists( 'software' ) ) {
		$query->set( 'post_type', array( 'software' ) );
	} elseif ( 'merged' === $mode ) {
		$types = array( 'post' );

		if ( post_type_exists( 'software' ) ) {
			$types[] = 'software';
		}

		$query->set( 'post_type', $types );
	}

	$count = (int) zabisoft_mod( 'front_posts_count' );

	if ( $count > 0 ) {
		$query->set( 'posts_per_page', $count );
	}
}
add_action( 'pre_get_posts', 'zabisoft_front_page_query' );

/**
 * Social links configured in the Customizer.
 *
 * @return array
 */
function zabisoft_social_links() {
	$networks = array(
		'facebook' => array( 'label' => 'Facebook', 'icon' => 'M13 3h4v4h-3c-.6 0-1 .4-1 1v3h3.5l-.5 4H13v9H9v-9H6v-4h3V8c0-2.8 1.8-5 4-5z' ),
		'twitter'  => array( 'label' => 'X', 'icon' => 'M17.5 3h3.2l-7 8 7.3 10h-5.6l-4.4-6-5 6H2.8l7.3-8.6L3 3h5.7l4.1 5.6L17.5 3z' ),
		'youtube'  => array( 'label' => 'YouTube', 'icon' => 'M21.6 7.2c-.2-1-.9-1.8-1.9-2C17.9 4.8 12 4.8 12 4.8s-5.9 0-7.7.4c-1 .2-1.7 1-1.9 2C2 9 2 12 2 12s0 3 .4 4.8c.2 1 .9 1.8 1.9 2 1.8.4 7.7.4 7.7.4s5.9 0 7.7-.4c1-.2 1.7-1 1.9-2 .4-1.8.4-4.8.4-4.8s0-3-.4-4.8zM10 15.5v-7l6 3.5-6 3.5z' ),
		'telegram' => array( 'label' => 'Telegram', 'icon' => 'M21.9 4.3l-3.1 14.9c-.2 1-.9 1.2-1.7.8l-4.7-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.4-4.9 9-8.1c.4-.3-.1-.5-.6-.2L6.8 13.2l-4.7-1.5c-1-.3-1-1 .2-1.5l18.3-7c.9-.3 1.6.2 1.3 1.1z' ),
		'github'   => array( 'label' => 'GitHub', 'icon' => 'M12 2a10 10 0 00-3.2 19.5c.5.1.7-.2.7-.5v-1.8c-2.8.6-3.4-1.3-3.4-1.3-.5-1.2-1.1-1.5-1.1-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.9.8.1-.6.4-1.1.7-1.4-2.2-.3-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.7 1a9.4 9.4 0 015 0c1.9-1.3 2.7-1 2.7-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.4 4.7-4.6 5 .4.3.7.9.7 1.9v2.8c0 .3.2.6.7.5A10 10 0 0012 2z' ),
	);

	$links = array();

	foreach ( $networks as $key => $data ) {
		$url = (string) zabisoft_mod( 'social_' . $key );

		if ( '' === $url ) {
			continue;
		}

		$links[ $key ] = array(
			'label' => $data['label'],
			'icon'  => $data['icon'],
			'url'   => esc_url( $url ),
		);
	}

	return $links;
}
