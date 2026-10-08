<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<main id="main-content" class="site-main standard-page">
		<div class="wrap standard-page__wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<article <?php post_class( 'standard-page__article' ); ?>>
				<header class="standard-page__header"><p class="section-heading__kicker"><?php esc_html_e( 'Crisp & Air', 'crisp-and-air' ); ?></p><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?></header>
				<div class="entry-content standard-page__content"><?php the_content(); ?></div>
			</article>
		</div>
	</main>
	<?php
endwhile;
get_footer();
