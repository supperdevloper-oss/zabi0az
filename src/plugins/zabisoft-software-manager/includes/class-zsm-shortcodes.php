<?php
/**
 * Shortcodes, so software information can be placed anywhere.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the shortcodes.
 */
class ZSM_Shortcodes {

	/**
	 * Hook up.
	 */
	public function __construct() {
		add_shortcode( 'zsm_software_grid', array( $this, 'software_grid' ) );
		add_shortcode( 'zsm_downloads', array( $this, 'downloads' ) );
		add_shortcode( 'zsm_specs', array( $this, 'specs' ) );
		add_shortcode( 'zsm_requirements', array( $this, 'requirements' ) );
		add_shortcode( 'zsm_link_status', array( $this, 'link_status' ) );
	}

	/**
	 * Read the mirrors of an entry.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	protected function mirrors( $post_id ) {
		if ( function_exists( 'zabisoft_get_mirrors' ) ) {
			return (array) zabisoft_get_mirrors( $post_id );
		}

		$raw = get_post_meta( $post_id, '_zs_mirrors', true );

		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * Grid of software entries.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function software_grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'category' => '',
				'platform' => '',
				'count'    => 6,
				'columns'  => 3,
				'orderby'  => 'date',
				'order'    => 'DESC',
				'title'    => '',
			),
			$atts,
			'zsm_software_grid'
		);

		$args = array(
			'post_type'      => 'software',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 24, absint( $atts['count'] ) ) ),
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => 'ASC' === strtoupper( $atts['order'] ) ? 'ASC' : 'DESC',
			'no_found_rows'  => true,
		);

		$tax_query = array();

		if ( $atts['category'] ) {
			$tax_query[] = array(
				'taxonomy' => 'software_category',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts['category'] ) ),
			);
		}

		if ( $atts['platform'] ) {
			$tax_query[] = array(
				'taxonomy' => 'software_platform',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts['platform'] ) ),
			);
		}

		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return '<p class="zsm-notice">' . esc_html__( 'No software entries found yet.', 'zabisoft-software-manager' ) . '</p>';
		}

		$columns = max( 1, min( 4, absint( $atts['columns'] ) ) );

		ob_start();

		if ( $atts['title'] ) {
			printf( '<h2 class="zsm-shortcode-title">%s</h2>', esc_html( $atts['title'] ) );
		}

		printf( '<div class="zsm-grid-software zsm-cols-%d">', (int) $columns );

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id  = get_the_ID();
			$version  = get_post_meta( $post_id, '_zs_version', true );
			$size     = get_post_meta( $post_id, '_zs_file_size', true );
			?>
			<article class="zsm-card">
				<a class="zsm-card__thumb" href="<?php the_permalink(); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) );
					} else {
						echo '<span class="zsm-card__placeholder" aria-hidden="true"></span>';
					}
					?>
				</a>
				<div class="zsm-card__body">
					<h3 class="zsm-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p class="zsm-card__meta">
						<?php
						$bits = array();

						if ( $version ) {
							/* translators: %s: version number. */
							$bits[] = esc_html( sprintf( __( 'Version %s', 'zabisoft-software-manager' ), $version ) );
						}

						if ( $size ) {
							$bits[] = esc_html( $size );
						}

						echo esc_html( implode( ' · ', $bits ) );
						?>
					</p>
				</div>
			</article>
			<?php
		}

		echo '</div>';

		wp_reset_postdata();

		return (string) ob_get_clean();
	}

	/**
	 * Download box for one entry.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function downloads( $atts ) {
		$atts    = shortcode_atts( array( 'id' => 0, 'title' => __( 'Download', 'zabisoft-software-manager' ) ), $atts, 'zsm_downloads' );
		$post_id = absint( $atts['id'] );

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id || 'software' !== get_post_type( $post_id ) ) {
			return '';
		}

		$mirrors = $this->mirrors( $post_id );

		if ( ! $mirrors ) {
			return '<p class="zsm-notice">' . esc_html__( 'No download link has been added yet.', 'zabisoft-software-manager' ) . '</p>';
		}

		$checker = class_exists( 'ZSM_Link_Checker' ) ? new ZSM_Link_Checker() : null;
		$new_tab = (int) get_option( 'zsm_open_new_tab', 1 );

		ob_start();
		?>
		<div class="zsm-downloads">
			<h2 class="zsm-downloads__title"><?php echo esc_html( $atts['title'] ); ?></h2>
			<ul class="zsm-downloads__list">
				<?php foreach ( $mirrors as $mirror ) : ?>
					<?php
					$url   = isset( $mirror['url'] ) ? $mirror['url'] : '';
					$host  = $url ? (string) wp_parse_url( $url, PHP_URL_HOST ) : '';
					$state = $checker ? $checker->cached_status( $url ) : array();
					?>
					<li class="zsm-download">
						<a class="zsm-download__button" href="<?php echo esc_url( $url ); ?>" rel="nofollow noopener"<?php echo $new_tab ? ' target="_blank"' : ''; ?>>
							<span class="zsm-download__label"><?php echo esc_html( $mirror['label'] ? $mirror['label'] : __( 'Download', 'zabisoft-software-manager' ) ); ?></span>
							<?php if ( $host ) : ?>
								<span class="zsm-download__host"><?php echo esc_html( $host ); ?></span>
							<?php endif; ?>
						</a>
						<span class="zsm-download__meta">
							<?php
							$bits = array_filter(
								array(
									$mirror['size'] ?? '',
									$mirror['type'] ?? '',
									$mirror['platform'] ?? '',
								)
							);

							echo esc_html( implode( ' · ', $bits ) );

							if ( $checker && ! empty( $state['time'] ) ) {
								printf(
									' <span class="zsm-download__status">%s</span>',
									esc_html(
										! empty( $state['ok'] )
											? __( 'link checked', 'zabisoft-software-manager' )
											: __( 'link needs attention', 'zabisoft-software-manager' )
									)
								);
							}
							?>
						</span>
						<?php if ( ! empty( $mirror['note'] ) ) : ?>
							<span class="zsm-download__note"><?php echo esc_html( $mirror['note'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="zsm-downloads__disclaimer">
				<?php echo esc_html( (string) apply_filters( 'zsm_disclosure_text', get_option( 'zsm_disclosure', '' ) ) ); ?>
			</p>
		</div>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Specification table.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function specs( $atts ) {
		$atts    = shortcode_atts( array( 'id' => 0 ), $atts, 'zsm_specs' );
		$post_id = absint( $atts['id'] ) ? absint( $atts['id'] ) : get_the_ID();

		if ( ! $post_id || 'software' !== get_post_type( $post_id ) ) {
			return '';
		}

		$rows = array(
			'_zs_version'       => __( 'Version', 'zabisoft-software-manager' ),
			'_zs_file_size'     => __( 'File size', 'zabisoft-software-manager' ),
			'_zs_setup_file'    => __( 'Setup file name', 'zabisoft-software-manager' ),
			'_zs_setup_type'    => __( 'Setup type', 'zabisoft-software-manager' ),
			'_zs_compatibility' => __( 'Compatibility', 'zabisoft-software-manager' ),
			'_zs_license'       => __( 'Licence', 'zabisoft-software-manager' ),
			'_zs_languages'     => __( 'Languages', 'zabisoft-software-manager' ),
			'_zs_release_date'  => __( 'Last update', 'zabisoft-software-manager' ),
			'_zs_developer'     => __( 'Developer', 'zabisoft-software-manager' ),
		);

		$output = '';

		foreach ( $rows as $key => $label ) {
			$value = get_post_meta( $post_id, $key, true );

			if ( '' === $value || null === $value ) {
				continue;
			}

			$output .= sprintf(
				'<tr><th scope="row">%s</th><td>%s</td></tr>',
				esc_html( $label ),
				esc_html( $value )
			);
		}

		if ( ! $output ) {
			return '';
		}

		return '<table class="zsm-specs"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * System requirements table.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function requirements( $atts ) {
		$atts    = shortcode_atts( array( 'id' => 0 ), $atts, 'zsm_requirements' );
		$post_id = absint( $atts['id'] ) ? absint( $atts['id'] ) : get_the_ID();

		if ( ! $post_id || 'software' !== get_post_type( $post_id ) ) {
			return '';
		}

		$rows = array(
			'_zs_req_os'    => __( 'Operating system', 'zabisoft-software-manager' ),
			'_zs_req_cpu'   => __( 'Processor', 'zabisoft-software-manager' ),
			'_zs_req_ram'   => __( 'Memory (RAM)', 'zabisoft-software-manager' ),
			'_zs_req_disk'  => __( 'Hard disk', 'zabisoft-software-manager' ),
			'_zs_req_gpu'   => __( 'Graphics', 'zabisoft-software-manager' ),
			'_zs_req_other' => __( 'Other', 'zabisoft-software-manager' ),
		);

		$output = '';

		foreach ( $rows as $key => $label ) {
			$value = get_post_meta( $post_id, $key, true );

			if ( '' === $value || null === $value ) {
				continue;
			}

			$output .= sprintf(
				'<tr><th scope="row">%s</th><td>%s</td></tr>',
				esc_html( $label ),
				esc_html( $value )
			);
		}

		if ( ! $output ) {
			return '';
		}

		return '<table class="zsm-requirements"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * Print the last verification result of every mirror.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function link_status( $atts ) {
		$atts    = shortcode_atts( array( 'id' => 0 ), $atts, 'zsm_link_status' );
		$post_id = absint( $atts['id'] ) ? absint( $atts['id'] ) : get_the_ID();

		if ( ! $post_id || ! class_exists( 'ZSM_Link_Checker' ) ) {
			return '';
		}

		$checker = new ZSM_Link_Checker();
		$rows    = '';

		foreach ( $this->mirrors( $post_id ) as $mirror ) {
			if ( empty( $mirror['url'] ) ) {
				continue;
			}

			$state = $checker->cached_status( $mirror['url'] );

			if ( empty( $state['time'] ) ) {
				continue;
			}

			$rows .= sprintf(
				'<li><span class="zsm-status zsm-status--%1$s">%2$s</span> %3$s <code>%4$s</code></li>',
				! empty( $state['ok'] ) ? 'ok' : 'bad',
				esc_html( ! empty( $state['ok'] ) ? __( 'OK', 'zabisoft-software-manager' ) : __( 'Problem', 'zabisoft-software-manager' ) ),
				esc_html( $mirror['label'] ?? '' ),
				esc_html( (string) wp_parse_url( $mirror['url'], PHP_URL_HOST ) )
			);
		}

		if ( ! $rows ) {
			return '';
		}

		return '<ul class="zsm-link-status">' . $rows . '</ul>';
	}
}
