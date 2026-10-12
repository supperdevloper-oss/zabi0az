<?php
/**
 * Template Name: Sitemap page
 * Template Description: Human readable sitemap with categories, pages and latest entries.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--page zs-entry--sitemap' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>
		</header>

		<?php
		if ( trim( get_the_content() ) ) :
			?>
			<div class="zs-entry__content entry-content"><?php the_content(); ?></div>
			<?php
		endif;
		?>

		<div class="zs-sitemap">
			<?php
			$zs_blocks = array();

			if ( post_type_exists( 'software' ) ) {
				$zs_blocks[] = array(
					'title' => __( 'Software categories', 'zabisoft' ),
					'terms' => 'software_category',
				);
			}

			$zs_blocks[] = array(
				'title' => __( 'Blog categories', 'zabisoft' ),
				'terms' => 'category',
			);

			if ( taxonomy_exists( 'software_platform' ) ) {
				$zs_blocks[] = array(
					'title' => __( 'Platforms', 'zabisoft' ),
					'terms' => 'software_platform',
				);
			}

			foreach ( $zs_blocks as $zs_block ) :
				$zs_terms = get_terms(
					array(
						'taxonomy'   => $zs_block['terms'],
						'hide_empty' => true,
						'number'     => 40,
					)
				);

				if ( is_wp_error( $zs_terms ) || ! $zs_terms ) {
					continue;
				}
				?>
				<section class="zs-widget">
					<h2 class="zs-widget__title"><?php echo esc_html( $zs_block['title'] ); ?></h2>
					<ul>
						<?php foreach ( $zs_terms as $zs_term ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $zs_term ) ); ?>"><?php echo esc_html( $zs_term->name ); ?></a>
								<span class="zs-count">(<?php echo esc_html( number_format_i18n( $zs_term->count ) ); ?>)</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
				<?php
			endforeach;
			?>

			<section class="zs-widget">
				<h2 class="zs-widget__title"><?php esc_html_e( 'Pages', 'zabisoft' ); ?></h2>
				<ul>
					<?php
					wp_list_pages(
						array(
							'title_li' => '',
							'depth'    => 1,
						)
					);
					?>
				</ul>
			</section>

			<?php
			$zs_types = array( 'post' );

			if ( post_type_exists( 'software' ) ) {
				$zs_types[] = 'software';
			}

			$zs_latest = new WP_Query(
				array(
					'post_type'      => $zs_types,
					'posts_per_page' => 20,
					'no_found_rows'  => true,
				)
			);

			if ( $zs_latest->have_posts() ) :
				?>
				<section class="zs-widget">
					<h2 class="zs-widget__title"><?php esc_html_e( 'Latest entries', 'zabisoft' ); ?></h2>
					<ul>
						<?php
						while ( $zs_latest->have_posts() ) :
							$zs_latest->the_post();
							?>
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
					</ul>
				</section>
				<?php
				wp_reset_postdata();
			endif;
			?>

			<section class="zs-widget">
				<h2 class="zs-widget__title"><?php esc_html_e( 'Feeds', 'zabisoft' ); ?></h2>
				<ul>
					<li><a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'RSS feed', 'zabisoft' ); ?></a></li>
					<li><a href="<?php echo esc_url( get_bloginfo( 'comments_rss2_url' ) ); ?>"><?php esc_html_e( 'Comments feed', 'zabisoft' ); ?></a></li>
					<?php if ( function_exists( 'get_sitemap_url' ) ) : ?>
						<li><a href="<?php echo esc_url( get_sitemap_url( 'index' ) ); ?>"><?php esc_html_e( 'XML sitemap', 'zabisoft' ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php esc_html_e( 'Search', 'zabisoft' ); ?></a></li>
				</ul>
			</section>
		</div>
	</article>
	<?php
endwhile;

get_footer();
