<?php
/**
 * Admin list columns for software entries.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add useful columns to the software list screen.
 */
class ZSM_Admin_Columns {

	/**
	 * Hook up.
	 */
	public function __construct() {
		add_filter( 'manage_software_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_software_posts_custom_column', array( $this, 'render' ), 10, 2 );
		add_filter( 'manage_edit-software_sortable_columns', array( $this, 'sortable' ) );
		add_action( 'restrict_manage_posts', array( $this, 'filters' ) );
		add_action( 'admin_post_zsm_check_links', array( $this, 'handle_bulk_check' ) );
		add_action( 'admin_notices', array( $this, 'show_check_result' ) );
	}

	/**
	 * Column set.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function columns( $columns ) {
		$new = array();

		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;

			if ( 'title' === $key ) {
				$new['zsm_thumb']   = __( 'Cover', 'zabisoft-software-manager' );
				$new['zsm_version'] = __( 'Version', 'zabisoft-software-manager' );
				$new['zsm_size']    = __( 'Size', 'zabisoft-software-manager' );
				$new['zsm_os']      = __( 'System', 'zabisoft-software-manager' );
				$new['zsm_mirrors'] = __( 'Mirrors', 'zabisoft-software-manager' );
				$new['zsm_status']  = __( 'Link check', 'zabisoft-software-manager' );
			}
		}

		return $new;
	}

	/**
	 * Render the custom columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render( $column, $post_id ) {
		switch ( $column ) {
			case 'zsm_thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 60, 45 ), array( 'style' => 'border-radius:4px;height:auto;' ) );
				} else {
					echo '<span class="dashicons dashicons-format-image" style="color:#c3c4c7"></span>';
				}
				break;

			case 'zsm_version':
				echo esc_html( (string) get_post_meta( $post_id, '_zs_version', true ) );
				break;

			case 'zsm_size':
				echo esc_html( (string) get_post_meta( $post_id, '_zs_file_size', true ) );
				break;

			case 'zsm_os':
				echo esc_html( (string) get_post_meta( $post_id, '_zs_req_os', true ) );
				break;

			case 'zsm_mirrors':
				$mirrors = get_post_meta( $post_id, '_zs_mirrors', true );
				$count   = is_array( $mirrors ) ? count( $mirrors ) : 0;

				if ( $count ) {
					printf( '<strong>%d</strong>', (int) $count );
				} else {
					echo '<span class="zsm-missing">' . esc_html__( 'none', 'zabisoft-software-manager' ) . '</span>';
				}
				break;

			case 'zsm_status':
				$mirrors = get_post_meta( $post_id, '_zs_mirrors', true );

				if ( ! is_array( $mirrors ) || ! $mirrors ) {
					echo '&mdash;';
					break;
				}

				$checker  = new ZSM_Link_Checker();
				$problems = 0;

				foreach ( $mirrors as $mirror ) {
					if ( empty( $mirror['url'] ) ) {
						continue;
					}

					$status = $checker->cached_status( $mirror['url'] );

					if ( empty( $status['ok'] ) ) {
						$problems++;
					}
				}

				if ( $problems ) {
					printf(
						'<span class="zsm-status zsm-status--bad">%s</span>',
						esc_html( sprintf( /* translators: %d: number of mirrors. */ _n( '%d link with a problem', '%d links with a problem', $problems, 'zabisoft-software-manager' ), $problems ) )
					);
				} else {
					printf(
						'<span class="zsm-status zsm-status--ok">%s</span>',
						esc_html__( 'all links checked', 'zabisoft-software-manager' )
					);
				}
				break;
		}
	}

	/**
	 * Sortable columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function sortable( $columns ) {
		$columns['zsm_version'] = 'zsm_version';

		return $columns;
	}

	/**
	 * Extra filters above the list.
	 *
	 * @return void
	 */
	public function filters() {
		global $typenow;

		if ( 'software' !== $typenow ) {
			return;
		}

		$platform = isset( $_GET['software_platform'] ) ? sanitize_title( wp_unslash( $_GET['software_platform'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_dropdown_categories(
			array(
				'show_option_all' => __( 'All platforms', 'zabisoft-software-manager' ),
				'taxonomy'        => 'software_platform',
				'name'            => 'software_platform',
				'orderby'         => 'name',
				'selected'        => $platform,
				'hierarchical'    => false,
				'hide_empty'      => false,
				'value_field'     => 'slug',
			)
		);

		printf(
			'<a href="%1$s" class="button" style="margin-left:6px">%2$s</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=zsm_check_links' ), 'zsm_check_links' ) ),
			esc_html__( 'Check every download link now', 'zabisoft-software-manager' )
		);
	}

	/**
	 * Run a bulk link check (admin-post handler, not AJAX).
	 *
	 * @return void
	 */
	public function handle_bulk_check() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'zabisoft-software-manager' ) );
		}

		check_admin_referer( 'zsm_check_links' );

		$checker = new ZSM_Link_Checker();
		$posts   = get_posts(
			array(
				'post_type'      => 'software',
				'posts_per_page' => 50,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			)
		);

		$checked  = 0;
		$problems = 0;

		foreach ( $posts as $post_id ) {
			$results = $checker->check_post( $post_id, true );

			foreach ( $results as $result ) {
				$checked++;

				if ( empty( $result['ok'] ) ) {
					$problems++;
				}
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type'      => 'software',
					'zsm_checked'    => $checked,
					'zsm_problems'   => $problems,
				),
				admin_url( 'edit.php' )
			)
		);
		exit;
	}

	/**
	 * Notice after a bulk check.
	 *
	 * @return void
	 */
	public function show_check_result() {
		if ( ! isset( $_GET['zsm_checked'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$checked  = absint( wp_unslash( $_GET['zsm_checked'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$problems = isset( $_GET['zsm_problems'] ) ? absint( wp_unslash( $_GET['zsm_problems'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			$problems ? 'warning' : 'success',
			esc_html(
				sprintf(
					/* translators: 1: checked links, 2: problem count. */
					__( 'Link check finished: %1$d URLs inspected, %2$d with a problem.', 'zabisoft-software-manager' ),
					$checked,
					$problems
				)
			)
		);
	}
}
