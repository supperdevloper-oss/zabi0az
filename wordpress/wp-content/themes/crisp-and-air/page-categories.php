<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$root = get_term_by( 'slug', 'air-fryer-recipes', 'category' );
$terms = ca_theme_category_terms( 100 );
?>
<main id="main-content" class="site-main category-directory">
	<section class="archive-hero archive-hero--compact">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<p class="section-heading__kicker"><?php esc_html_e( 'Explore by craving', 'crisp-and-air' ); ?></p>
			<h1><?php esc_html_e( 'Air fryer recipe categories', 'crisp-and-air' ); ?></h1>
			<p><?php esc_html_e( 'A simple guide to the recipes in the collection. Choose a category to browse its latest ideas.', 'crisp-and-air' ); ?></p>
			<?php ca_theme_search_form( 'ca-search-form--archive' ); ?>
		</div>
	</section>
	<div class="wrap section-block">
		<?php if ( $terms ) : ?>
			<div class="category-directory__grid">
				<?php foreach ( $terms as $index => $term ) : ?>
					<a class="category-directory-card category-card--<?php echo esc_attr( ( $index % 4 ) + 1 ); ?>" href="<?php echo esc_url( get_category_link( $term ) ); ?>">
						<span class="category-directory-card__icon" aria-hidden="true"><?php echo esc_html( ca_theme_category_icon( $term->slug ) ); ?></span>
						<span class="category-directory-card__content"><strong><?php echo esc_html( $term->name ); ?></strong><span><?php echo esc_html( $term->description ? wp_trim_words( wp_strip_all_tags( $term->description ), 13 ) : __( 'Explore recipes in this collection.', 'crisp-and-air' ) ); ?></span><?php if ( $term->count ) : ?><small><?php printf( esc_html( _n( '%s recipe', '%s recipes', (int) $term->count, 'crisp-and-air' ) ), esc_html( number_format_i18n( (int) $term->count ) ) ); ?></small><?php endif; ?></span>
						<span class="category-directory-card__arrow" aria-hidden="true">↗</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="empty-library"><h2><?php esc_html_e( 'Categories are ready to be curated.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Create useful recipe categories in the WordPress dashboard and they will appear here.', 'crisp-and-air' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $root && ! is_wp_error( $root ) ) : ?><p class="category-directory__note"><?php esc_html_e( 'We keep category collections focused and useful; not every search phrase needs its own category.', 'crisp-and-air' ); ?></p><?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
