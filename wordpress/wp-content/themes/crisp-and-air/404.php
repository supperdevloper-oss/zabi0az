<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="main-content" class="site-main not-found-page">
	<div class="wrap not-found-page__inner">
		<span class="section-heading__kicker"><?php esc_html_e( 'Nothing crispy here', 'crisp-and-air' ); ?></span>
		<p class="not-found-page__number">404</p>
		<h1><?php esc_html_e( 'Let’s get you back to something good.', 'crisp-and-air' ); ?></h1>
		<p><?php esc_html_e( 'That page may have moved. Search the recipe collection or browse by category.', 'crisp-and-air' ); ?></p>
		<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
		<a class="button button--dark" href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'Browse categories', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a>
	</div>
</main>
<?php get_footer(); ?>
