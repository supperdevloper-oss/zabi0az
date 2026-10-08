<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="main-content" class="site-main archive-page">
	<div class="wrap archive-content">
		<header class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'Crisp & Air', 'crisp-and-air' ); ?></span><h1><?php esc_html_e( 'Air fryer recipes', 'crisp-and-air' ); ?></h1></header>
		<?php if ( have_posts() ) : ?>
			<div class="recipe-grid recipe-grid--three"><?php while ( have_posts() ) : the_post(); ca_theme_recipe_card( get_the_ID() ); endwhile; ?></div>
			<?php ca_theme_pagination(); ?>
		<?php else : ?><div class="empty-library"><h2><?php esc_html_e( 'Your recipe collection is ready.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Add your first recipe from the WordPress dashboard.', 'crisp-and-air' ); ?></p></div><?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
