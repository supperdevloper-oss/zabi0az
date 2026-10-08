<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$paged = max( 1, (int) get_query_var( 'paged' ) );
$popular = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => $paged,
		'meta_key'       => '_ca_popular',
		'meta_value'     => '1',
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<main id="main-content" class="site-main archive-page">
	<section class="archive-hero archive-hero--compact">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<p class="section-heading__kicker"><?php esc_html_e( 'Worth keeping close', 'crisp-and-air' ); ?></p>
			<h1><?php esc_html_e( 'Popular air fryer recipes', 'crisp-and-air' ); ?></h1>
			<p><?php esc_html_e( 'A hand-curated collection of recipes to make your next meal easier.', 'crisp-and-air' ); ?></p>
			<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
		</div>
	</section>
	<div class="wrap archive-content">
		<?php if ( $popular->have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Popular air fryer recipes', 'crisp-and-air' ); ?></h2>
			<div class="recipe-grid recipe-grid--three">
				<?php while ( $popular->have_posts() ) : $popular->the_post(); ca_theme_recipe_card( get_the_ID() ); endwhile; ?>
			</div>
			<?php ca_theme_pagination( $popular ); ?>
		<?php else : ?>
			<div class="empty-library"><span class="empty-library__icon" aria-hidden="true">✦</span><div><h2><?php esc_html_e( 'This collection is ready for its first pick.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'In the recipe editor, choose “Include in Popular Recipes” for a published recipe you want to feature here.', 'crisp-and-air' ); ?></p></div><a class="button button--outline" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse all recipes', 'crisp-and-air' ); ?></a></div>
		<?php endif; ?>
	</div>
</main>
<?php wp_reset_postdata(); get_footer(); ?>
