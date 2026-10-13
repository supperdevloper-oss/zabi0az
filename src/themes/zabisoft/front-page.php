<?php
/**
 * Front page.
 *
 * Works both when “Your latest posts” is selected and when a static page is
 * used as the front page.
 *
 * @package ZabiSoft
 */

get_header();

$zs_is_static = is_page();
$zs_has_hero  = (bool) zabisoft_mod( 'front_hero' );

if ( $zs_has_hero ) :
	?>
	<section class="zs-hero">
		<div class="zs-container zs-hero__inner">
			<div class="zs-hero__copy">
				<h2><?php echo esc_html( zabisoft_mod( 'front_hero_title' ) ); ?></h2>
				<p><?php echo esc_html( zabisoft_mod( 'front_hero_text' ) ); ?></p>

				<?php
				$zs_quick = zabisoft_mod( 'front_quick_links' );

				if ( $zs_quick ) :
					$zs_quick_items = array_filter( array_map( 'trim', explode( ',', $zs_quick ) ) );
					?>
					<ul class="zs-hero__quick">
						<?php foreach ( $zs_quick_items as $zs_item ) : ?>
							<li><a href="<?php echo esc_url( add_query_arg( 's', urlencode( $zs_item ), home_url( '/' ) ) ); ?>"><?php echo esc_html( $zs_item ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( zabisoft_mod( 'front_hero_search' ) ) : ?>
				<div class="zs-hero__search">
					<?php get_search_form( array( 'zs_placeholder' => __( 'Type a program name, e.g. Office, VPN, backup…', 'zabisoft' ) ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
endif;

if ( $zs_is_static ) :
	// Static front page: print the page content first, then a short listing.
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'zs-entry' ); ?>>
			<div class="zs-entry__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
endif;

if ( zabisoft_mod( 'front_category_strip' ) ) :
	$zs_taxonomy = taxonomy_exists( 'software_category' ) ? 'software_category' : 'category';
	$zs_terms    = get_terms(
		array(
			'taxonomy'   => $zs_taxonomy,
			'hide_empty' => true,
			'number'     => 8,
			'parent'     => 0,
		)
	);

	if ( ! is_wp_error( $zs_terms ) && $zs_terms ) :
		?>
		<section class="zs-section zs-section--categories">
			<div class="zs-section__head">
				<h2 class="zs-section__title"><?php esc_html_e( 'Browse by category', 'zabisoft' ); ?></h2>
			</div>
			<ul class="zs-cat-strip">
				<?php foreach ( $zs_terms as $zs_term ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $zs_term ) ); ?>">
							<span><?php echo esc_html( $zs_term->name ); ?></span>
							<span class="count"><?php echo esc_html( number_format_i18n( $zs_term->count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	endif;
endif;

if ( $zs_is_static ) :
	// Second listing for a static front page (single page, no pagination).
	$zs_types = array( 'post' );

	if ( post_type_exists( 'software' ) ) {
		$zs_types[] = 'software';
	}

	$zs_listing = new WP_Query(
		array(
			'post_type'      => $zs_types,
			'posts_per_page' => (int) zabisoft_mod( 'front_posts_count' ),
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	if ( $zs_listing->have_posts() ) :
		?>
		<section class="zs-section">
			<div class="zs-section__head">
				<h2 class="zs-section__title"><?php echo esc_html( zabisoft_mod( 'front_section_title' ) ); ?></h2>
				<a class="zs-section__link" href="<?php echo esc_url( get_post_type_archive_link( 'software' ) ? get_post_type_archive_link( 'software' ) : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'View all', 'zabisoft' ); ?>
				</a>
			</div>

			<div class="<?php echo esc_attr( zabisoft_list_class() ); ?>">
				<?php
				while ( $zs_listing->have_posts() ) :
					$zs_listing->the_post();
					if ( 'software' === get_post_type() ) {
						get_template_part( 'template-parts/content', 'software' );
					} else {
						get_template_part( 'template-parts/content', 'post' );
					}
				endwhile;
				?>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
else :
	$zs_sections = (bool) zabisoft_mod( 'front_sections' );

	if ( $zs_sections ) :
		?>
		<section class="zs-section">
			<div class="zs-section__head">
				<h2 class="zs-section__title"><?php echo esc_html( zabisoft_mod( 'front_section_title' ) ); ?></h2>
				<?php
				$zs_archive = get_post_type_archive_link( 'software' );
				if ( $zs_archive ) :
					?>
					<a class="zs-section__link" href="<?php echo esc_url( $zs_archive ); ?>"><?php esc_html_e( 'All software', 'zabisoft' ); ?></a>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/loop' ); ?>
		</section>
		<?php
	else :
		get_template_part( 'template-parts/loop' );
	endif;
endif;

get_footer();
