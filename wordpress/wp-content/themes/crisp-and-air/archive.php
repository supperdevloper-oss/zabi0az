<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$queried = get_queried_object();
$title   = is_category() ? single_cat_title( '', false ) : get_the_archive_title();
$description = is_category() || is_tag() ? term_description() : get_the_archive_description();
$children = ( is_category() && isset( $queried->term_id ) ) ? get_categories( array( 'parent' => (int) $queried->term_id, 'hide_empty' => false ) ) : array();
?>
<main id="main-content" class="site-main archive-page">
	<section class="archive-hero">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<p class="section-heading__kicker"><?php esc_html_e( 'Browse the collection', 'crisp-and-air' ); ?></p>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $description ) : ?><div class="archive-description"><?php echo wp_kses_post( $description ); ?></div><?php else : ?><p><?php esc_html_e( 'Find your next air-fryer favorite with clear steps and practical cooking guidance.', 'crisp-and-air' ); ?></p><?php endif; ?>
			<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
		</div>
	</section>
	<div class="wrap archive-content">
		<?php if ( $children ) : ?>
			<nav class="subcategory-strip" aria-label="<?php esc_attr_e( 'Subcategories', 'crisp-and-air' ); ?>">
				<?php foreach ( $children as $child ) : ?><a href="<?php echo esc_url( get_category_link( $child->term_id ) ); ?>"><?php echo esc_html( $child->name ); ?><span aria-hidden="true">↗</span></a><?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<div class="archive-toolbar"><p><?php printf( esc_html( _n( '%s recipe', '%s recipes', (int) $wp_query->found_posts, 'crisp-and-air' ) ), esc_html( number_format_i18n( (int) $wp_query->found_posts ) ) ); ?></p><?php if ( is_tag() ) : ?><span class="archive-toolbar__note"><?php esc_html_e( 'Showing recipes tagged with this topic.', 'crisp-and-air' ); ?></span><?php endif; ?></div>
		<?php if ( have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Recipes in %s', 'crisp-and-air' ), $title ) ); ?></h2>
			<div class="recipe-grid recipe-grid--three">
				<?php while ( have_posts() ) : the_post(); ca_theme_recipe_card( get_the_ID() ); if ( 3 === $wp_query->current_post ) { ca_theme_ad_slot( 'ca-archive-infeed', __( 'In-feed advertisement', 'crisp-and-air' ) ); } endwhile; ?>
			</div>
			<?php ca_theme_pagination(); ?>
		<?php else : ?>
			<div class="empty-library"><span class="empty-library__icon" aria-hidden="true">✳</span><div><h2><?php esc_html_e( 'Nothing here just yet.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Try another category or search the full recipe collection.', 'crisp-and-air' ); ?></p></div><a class="button button--outline" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse all recipes', 'crisp-and-air' ); ?></a></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
