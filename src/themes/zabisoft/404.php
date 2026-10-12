<?php
/**
 * 404 page.
 *
 * @package ZabiSoft
 */

get_header();

zabisoft_breadcrumbs();
?>

<section class="zs-404">
	<p class="zs-404__code">404</p>
	<h1 class="zs-404__title"><?php esc_html_e( 'This page could not be found', 'zabisoft' ); ?></h1>
	<p class="zs-404__text">
		<?php esc_html_e( 'The link may be broken, or the program may have been renamed. Try searching for what you need, or start from one of the categories below.', 'zabisoft' ); ?>
	</p>

	<?php get_search_form( array( 'zs_placeholder' => __( 'Search software, tools, tutorials…', 'zabisoft' ) ) ); ?>

	<?php
	$zs_taxonomy = taxonomy_exists( 'software_category' ) ? 'software_category' : 'category';
	$zs_terms    = get_terms(
		array(
			'taxonomy'   => $zs_taxonomy,
			'hide_empty' => true,
			'number'     => 10,
			'parent'     => 0,
		)
	);

	if ( ! is_wp_error( $zs_terms ) && $zs_terms ) :
		?>
		<ul class="zs-404__cats">
			<?php foreach ( $zs_terms as $zs_term ) : ?>
				<li>
					<a class="zs-badge zs-badge--plain" href="<?php echo esc_url( get_term_link( $zs_term ) ); ?>">
						<?php echo esc_html( $zs_term->name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php
	$zs_recent = new WP_Query(
		array(
			'posts_per_page' => 3,
			'no_found_rows'  => true,
		)
	);

	if ( $zs_recent->have_posts() ) :
		?>
		<div class="zs-404__recent">
			<h2 class="zs-related__title"><?php esc_html_e( 'Latest entries', 'zabisoft' ); ?></h2>
			<div class="zs-grid zs-grid--3">
				<?php
				while ( $zs_recent->have_posts() ) :
					$zs_recent->the_post();
					get_template_part( 'template-parts/card' );
				endwhile;
				?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	endif;
	?>
</section>

<?php
get_footer();
