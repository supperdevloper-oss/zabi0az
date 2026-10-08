<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$search_terms = ca_theme_category_terms( 6 );
?>
<main id="main-content" class="site-main search-page">
	<section class="archive-hero archive-hero--search">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<p class="section-heading__kicker"><?php esc_html_e( 'Find something delicious', 'crisp-and-air' ); ?></p>
			<h1><?php printf( esc_html__( 'Search recipes for “%s”', 'crisp-and-air' ), esc_html( get_search_query() ) ); ?></h1>
			<p><?php esc_html_e( 'Search by recipe name, ingredient, or category.', 'crisp-and-air' ); ?></p>
			<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
			<?php if ( $search_terms ) : ?><div class="search-category-links"><span><?php esc_html_e( 'Browse a category:', 'crisp-and-air' ); ?></span><?php foreach ( $search_terms as $term ) : ?><a href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endforeach; ?></div><?php endif; ?>
		</div>
	</section>
	<div class="wrap archive-content">
		<div class="archive-toolbar"><p><?php printf( esc_html( _n( '%s recipe found', '%s recipes found', (int) $wp_query->found_posts, 'crisp-and-air' ) ), esc_html( number_format_i18n( (int) $wp_query->found_posts ) ) ); ?></p></div>
		<?php if ( have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Search results', 'crisp-and-air' ); ?></h2>
			<div class="recipe-grid recipe-grid--three">
				<?php while ( have_posts() ) : the_post(); ca_theme_recipe_card( get_the_ID() ); if ( 3 === $wp_query->current_post ) { ca_theme_ad_slot( 'ca-archive-infeed', __( 'In-feed advertisement', 'crisp-and-air' ) ); } endwhile; ?>
			</div>
			<?php ca_theme_pagination(); ?>
		<?php else : ?>
			<div class="empty-library empty-library--search"><span class="empty-library__icon" aria-hidden="true">⌕</span><div><h2><?php esc_html_e( 'No recipe found this time.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Try a shorter search, a single ingredient, or one of the categories above.', 'crisp-and-air' ); ?></p></div></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
