<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) :
	the_post();
	$post_id       = get_the_ID();
	$hero_image_id = get_post_thumbnail_id( $post_id );
	$hero_alt      = $hero_image_id ? get_post_meta( $hero_image_id, '_wp_attachment_image_alt', true ) : '';
	$hero_alt      = $hero_alt ? $hero_alt : get_the_title( $post_id );
	$ingredients    = ca_recipe_lines( ca_recipe_meta( $post_id, 'ingredients' ) );
	$instructions   = ca_recipe_lines( ca_recipe_meta( $post_id, 'instructions' ) );
	$prep           = absint( ca_recipe_meta( $post_id, 'prep_minutes' ) );
	$cook           = absint( ca_recipe_meta( $post_id, 'cook_minutes' ) );
	$total          = $prep + $cook;
	$servings       = ca_recipe_meta( $post_id, 'servings' );
	$temp           = ca_recipe_meta( $post_id, 'air_fryer_temp' );
	$course          = ca_recipe_meta( $post_id, 'course' );
	$calories        = absint( ca_recipe_meta( $post_id, 'calories' ) );
	$tips            = ca_recipe_lines( ca_recipe_meta( $post_id, 'tips' ) );
	$variations      = ca_recipe_lines( ca_recipe_meta( $post_id, 'variations' ) );
	$substitutions   = ca_recipe_lines( ca_recipe_meta( $post_id, 'substitutions' ) );
	$faqs            = ca_recipe_faq_items( ca_recipe_meta( $post_id, 'faqs' ) );
	$categories      = get_the_category();
	$primary_category = '';
	if ( $categories ) {
		foreach ( $categories as $candidate ) {
			if ( 'air-fryer-recipes' !== $candidate->slug ) {
				$primary_category = $candidate;
				break;
			}
		}
	}
	?>
	<main id="main-content" class="site-main recipe-page">
		<div class="wrap">
			<?php ca_theme_breadcrumbs(); ?>
			<div class="recipe-layout">
				<article <?php post_class( 'recipe-article' ); ?>>
					<header class="recipe-header">
						<?php if ( $primary_category ) : ?><a class="eyebrow-link" href="<?php echo esc_url( get_category_link( $primary_category ) ); ?>"><?php echo esc_html( $primary_category->name ); ?></a><?php else : ?><span class="eyebrow-link eyebrow-link--static"><?php esc_html_e( 'Air Fryer Recipe', 'crisp-and-air' ); ?></span><?php endif; ?>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?><p class="recipe-header__deck"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
						<div class="byline">
							<?php $author_photo = ca_recipe_author_photo( get_the_author_meta( 'ID' ), 'thumbnail' ); ?>
							<a class="byline__avatar" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'More recipes by %s', 'crisp-and-air' ), get_the_author() ) ); ?>">
								<?php if ( $author_photo ) : ?><?php echo $author_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML is generated and escaped by WordPress. ?><?php else : ?><span aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_author(), 0, 1 ) ) ); ?></span><?php endif; ?>
							</a>
							<div><span><?php esc_html_e( 'By', 'crisp-and-air' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></span><span class="byline__date"><?php echo esc_html( get_the_date() ); ?><?php if ( get_the_modified_time( 'U' ) !== get_the_time( 'U' ) ) : ?> · <?php esc_html_e( 'Updated', 'crisp-and-air' ); ?> <?php echo esc_html( get_the_modified_date() ); ?><?php endif; ?></span></div>
							<button class="share-button" type="button" data-share-recipe aria-label="<?php esc_attr_e( 'Share this recipe', 'crisp-and-air' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.6"></circle><circle cx="6" cy="12" r="2.6"></circle><circle cx="18" cy="19" r="2.6"></circle><path d="m8.4 10.9 7.2-4.6M8.4 13.1l7.2 4.6"></path></svg><span><?php esc_html_e( 'Share', 'crisp-and-air' ); ?></span></button>
						</div>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="recipe-hero-image">
							<?php echo get_the_post_thumbnail( $post_id, 'ca-hero', array( 'class' => 'ca-image', 'alt' => $hero_alt, 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress image function escapes its attributes. ?>
							<?php $image_caption = get_the_post_thumbnail_caption(); if ( $image_caption ) : ?><figcaption><?php echo esc_html( $image_caption ); ?></figcaption><?php endif; ?>
						</figure>
					<?php endif; ?>

					<?php if ( $total || $servings || $course || $calories ) : ?>
						<div class="recipe-facts" aria-label="<?php esc_attr_e( 'Recipe overview', 'crisp-and-air' ); ?>">
							<?php if ( $prep ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Prep', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $prep ) ); ?></strong></div><?php endif; ?>
							<?php if ( $cook ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Cook', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $cook ) ); ?></strong></div><?php endif; ?>
							<?php if ( $total ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Total', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $total ) ); ?></strong></div><?php endif; ?>
							<?php if ( $servings ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Serves', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( $servings ); ?></strong></div><?php endif; ?>
							<?php if ( $calories ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Calories', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( number_format_i18n( $calories ) . ' / ' . __( 'serving', 'crisp-and-air' ) ); ?></strong></div><?php endif; ?>
							<?php if ( $course ) : ?><div class="recipe-facts__item"><span><?php esc_html_e( 'Course', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( $course ); ?></strong></div><?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $temp || $cook ) : ?>
						<div class="temperature-callout">
							<div class="temperature-callout__icon" aria-hidden="true">°</div>
							<div><span class="temperature-callout__label"><?php esc_html_e( 'Air fryer quick guide', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( $temp ? ca_theme_temperature_label( $temp ) : __( 'Check recipe instructions', 'crisp-and-air' ) ); ?><?php if ( $cook ) : ?><span aria-hidden="true"> · </span><?php echo esc_html( ca_recipe_display_minutes( $cook ) ); ?><?php endif; ?></strong><p><?php esc_html_e( 'Preheat only if your appliance or recipe calls for it. Timing can vary by model and portion size.', 'crisp-and-air' ); ?></p></div>
						</div>
					<?php endif; ?>

					<?php if ( has_excerpt() ) : ?><p class="recipe-intro"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
					<?php ca_theme_ad_slot( 'ca-after-intro', __( 'Advertisement after recipe introduction', 'crisp-and-air' ) ); ?>

					<div class="recipe-content-grid">
						<div class="recipe-content">
							<?php if ( get_the_content() ) : ?><div class="entry-content"><?php the_content(); ?></div><?php endif; ?>
							<?php if ( $ingredients ) : ?>
								<section class="article-recipe-section" id="ingredients">
									<div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'Gather these', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Ingredients', 'crisp-and-air' ); ?></h2></div>
									<ul class="ingredient-list ingredient-list--article"><?php foreach ( $ingredients as $ingredient ) : ?><li><?php echo esc_html( $ingredient ); ?></li><?php endforeach; ?></ul>
								</section>
							<?php endif; ?>
							<?php if ( $instructions ) : ?>
								<section class="article-recipe-section" id="instructions">
									<div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'The method', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'How to make it', 'crisp-and-air' ); ?></h2></div>
									<ol class="instruction-list"><?php foreach ( $instructions as $index => $instruction ) : ?><li><span class="instruction-list__number" aria-hidden="true"><?php echo esc_html( $index + 1 ); ?></span><div><?php echo esc_html( $instruction ); ?></div></li><?php endforeach; ?></ol>
								</section>
							<?php endif; ?>
							<?php if ( $tips ) : ?><section class="article-recipe-section" id="expert-tips"><div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'A few useful details', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Tips for the best results', 'crisp-and-air' ); ?></h2></div><ul class="simple-list"><?php foreach ( $tips as $tip ) : ?><li><?php echo esc_html( $tip ); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
							<?php if ( $variations ) : ?><section class="article-recipe-section" id="variations"><div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'Make it yours', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Variations', 'crisp-and-air' ); ?></h2></div><ul class="simple-list"><?php foreach ( $variations as $variation ) : ?><li><?php echo esc_html( $variation ); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
							<?php if ( $substitutions ) : ?><section class="article-recipe-section" id="substitutions"><div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'If you need a swap', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Substitutions', 'crisp-and-air' ); ?></h2></div><ul class="simple-list"><?php foreach ( $substitutions as $substitution ) : ?><li><?php echo esc_html( $substitution ); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
							<?php foreach ( array( 'storage' => __( 'How to store leftovers', 'crisp-and-air' ), 'reheating' => __( 'How to reheat', 'crisp-and-air' ) ) as $key => $heading ) : $copy = ca_recipe_meta( $post_id, $key ); if ( $copy ) : ?>
								<section class="article-recipe-section article-recipe-section--note"><h2><?php echo esc_html( $heading ); ?></h2><p><?php echo nl2br( esc_html( $copy ) ); ?></p></section>
							<?php endif; endforeach; ?>
							<?php if ( $faqs ) : ?><section class="article-recipe-section faq-section" id="faq"><div class="section-heading"><span class="section-heading__kicker"><?php esc_html_e( 'Good to know', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Frequently asked questions', 'crisp-and-air' ); ?></h2></div><div class="faq-list"><?php foreach ( $faqs as $faq ) : ?><details><summary><?php echo esc_html( $faq['question'] ); ?></summary><div class="faq-list__answer"><p><?php echo esc_html( $faq['answer'] ); ?></p></div></details><?php endforeach; ?></div></section><?php endif; ?>
						</div>
						<aside class="recipe-sidebar" aria-label="<?php esc_attr_e( 'Recipe tools and advertising', 'crisp-and-air' ); ?>">
							<div class="recipe-jump-card"><span class="recipe-jump-card__title"><?php esc_html_e( 'Jump to', 'crisp-and-air' ); ?></span><?php if ( $ingredients ) : ?><a href="#ingredients"><?php esc_html_e( 'Ingredients', 'crisp-and-air' ); ?> <span aria-hidden="true">↘</span></a><?php endif; ?><?php if ( $instructions ) : ?><a href="#instructions"><?php esc_html_e( 'Instructions', 'crisp-and-air' ); ?> <span aria-hidden="true">↘</span></a><?php endif; ?><a href="#recipe-card"><?php esc_html_e( 'Printable recipe card', 'crisp-and-air' ); ?> <span aria-hidden="true">↘</span></a></div>
							<?php ca_theme_ad_slot( 'ca-sidebar', __( 'Sidebar advertisement', 'crisp-and-air' ) ); ?>
						</aside>
					</div>

					<?php ca_theme_ad_slot( 'ca-before-recipe', __( 'Advertisement before printable recipe', 'crisp-and-air' ) ); ?>
					<?php get_template_part( 'template-parts/recipe-card-printable', null, array( 'post_id' => $post_id ) ); ?>
					<?php ca_theme_ad_slot( 'ca-after-recipe', __( 'Advertisement after printable recipe', 'crisp-and-air' ) ); ?>

					<?php $author_id = (int) get_the_author_meta( 'ID' ); $author_bio = ca_recipe_author_bio( $author_id ); ?>
					<section class="author-card" aria-label="<?php esc_attr_e( 'About the author', 'crisp-and-air' ); ?>">
						<a class="author-card__avatar" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'More recipes by %s', 'crisp-and-air' ), get_the_author() ) ); ?>"><?php if ( ca_recipe_author_photo( $author_id, 'ca-author' ) ) : ?><?php echo ca_recipe_author_photo( $author_id, 'ca-author' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML from WordPress. ?><?php else : ?><span aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_author(), 0, 1 ) ) ); ?></span><?php endif; ?></a>
						<div><span class="author-card__kicker"><?php esc_html_e( 'Recipe by', 'crisp-and-air' ); ?></span><h2><a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php the_author(); ?></a></h2><?php if ( $author_bio ) : ?><p><?php echo esc_html( $author_bio ); ?></p><?php endif; ?><a class="text-link" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php esc_html_e( 'More recipes by this author', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
					</section>

					<?php
					$related_categories = wp_list_pluck( $categories, 'term_id' );
					$root_term = get_term_by( 'slug', 'air-fryer-recipes', 'category' );
					if ( $root_term ) {
						$related_categories = array_values( array_diff( $related_categories, array( (int) $root_term->term_id ) ) );
					}
					$related_args = array(
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => 3,
						'post__not_in'        => array( $post_id ),
					'ignore_sticky_posts' => true,
					'no_found_rows'        => true,
				);
					if ( $related_categories ) {
						$related_args['category__in'] = $related_categories;
					}
					$related = new WP_Query( $related_args );
					if ( ! $related->have_posts() ) {
						unset( $related_args['category__in'] );
						$related = new WP_Query( $related_args );
					}
					if ( $related->have_posts() ) :
						?>
						<section class="related-recipes section-block">
							<div class="section-heading section-heading--row"><div><span class="section-heading__kicker"><?php esc_html_e( 'Keep exploring', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'You may also like', 'crisp-and-air' ); ?></h2></div><a class="text-link" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'All recipes', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
							<div class="recipe-grid recipe-grid--three">
								<?php while ( $related->have_posts() ) : $related->the_post(); ca_theme_recipe_card( get_the_ID() ); endwhile; ?>
							</div>
						</section>
						<?php
					endif;
					wp_reset_postdata();
					?>
				</article>
			</div>
		</div>
	</main>
	<?php
endwhile;
get_footer();
