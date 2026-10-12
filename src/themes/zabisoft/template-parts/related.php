<?php
/**
 * Related posts (same categories, newest first).
 *
 * @package ZabiSoft
 */

$zs_cats = wp_get_post_categories( get_the_ID() );

if ( empty( $zs_cats ) ) {
	return;
}

$zs_related = new WP_Query(
	array(
		'category__in'        => $zs_cats,
		'post__not_in'        => array( get_the_ID() ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $zs_related->have_posts() ) {
	return;
}
?>
<section class="zs-related">
	<h2 class="zs-related__title"><?php esc_html_e( 'You may also like', 'zabisoft' ); ?></h2>

	<div class="zs-grid zs-grid--3">
		<?php
		while ( $zs_related->have_posts() ) :
			$zs_related->the_post();
			get_template_part( 'template-parts/card' );
		endwhile;
		?>
	</div>
</section>
<?php
wp_reset_postdata();
