<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="main-content" class="site-main archive-page">
	<section class="archive-hero archive-hero--compact">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<p class="section-heading__kicker"><?php esc_html_e( 'The recipe collection', 'crisp-and-air' ); ?></p>
			<h1><?php esc_html_e( 'All air fryer recipes', 'crisp-and-air' ); ?></h1>
			<p><?php esc_html_e( 'Browse practical recipes for weeknight dinners, sides, snacks, and something sweet.', 'crisp-and-air' ); ?></p>
			<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
		</div>
	</section>
	<div class="wrap archive-content">
		<div class="archive-toolbar"><p><?php printf( esc_html( _n( '%s recipe', '%s recipes', (int) $wp_query->found_posts, 'crisp-and-air' ) ), esc_html( number_format_i18n( (int) $wp_query->found_posts ) ) ); ?></p><a class="text-link" href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'Browse by category', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
		<?php if ( have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Latest air fryer recipes', 'crisp-and-air' ); ?></h2>
			<div class="recipe-grid recipe-grid--three">
				<?php while ( have_posts() ) : the_post(); ca_theme_recipe_card( get_the_ID() ); if ( 3 === $wp_query->current_post ) { ca_theme_ad_slot( 'ca-archive-infeed', __( 'In-feed advertisement', 'crisp-and-air' ) ); } endwhile; ?>
			</div>
			<?php ca_theme_pagination(); ?>
		<?php else : ?>
			<div class="empty-library"><span class="empty-library__icon" aria-hidden="true">✳</span><div><h2><?php esc_html_e( 'Your collection is taking shape.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Add recipes in WordPress and they will appear here automatically.', 'crisp-and-air' ); ?></p></div></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
