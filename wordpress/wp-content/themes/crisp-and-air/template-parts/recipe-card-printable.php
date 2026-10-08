<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$post_id      = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$ingredients  = ca_recipe_lines( ca_recipe_meta( $post_id, 'ingredients' ) );
$instructions = ca_recipe_lines( ca_recipe_meta( $post_id, 'instructions' ) );
$prep         = absint( ca_recipe_meta( $post_id, 'prep_minutes' ) );
$cook         = absint( ca_recipe_meta( $post_id, 'cook_minutes' ) );
$total        = $prep + $cook;
$servings     = ca_recipe_meta( $post_id, 'servings' );
$temp         = ca_recipe_meta( $post_id, 'air_fryer_temp' );
$course       = ca_recipe_meta( $post_id, 'course' );
$calories     = absint( ca_recipe_meta( $post_id, 'calories' ) );
$notes        = ca_recipe_meta( $post_id, 'notes' );
$description  = get_the_excerpt( $post_id );
if ( ! $description ) {
	$description = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 28 );
}
$thumbnail_id  = get_post_thumbnail_id( $post_id );
$thumbnail_alt = $thumbnail_id ? get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '';
$thumbnail_alt = $thumbnail_alt ? $thumbnail_alt : get_the_title( $post_id );
?>
<section class="printable-recipe-card" id="recipe-card" aria-labelledby="recipe-card-title">
	<header class="printable-recipe-card__header">
		<div>
			<span class="section-heading__kicker"><?php esc_html_e( 'Save this one', 'crisp-and-air' ); ?></span>
			<h2 id="recipe-card-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
		</div>
		<div class="printable-recipe-card__actions no-print">
			<button class="button button--outline" type="button" data-print-recipe><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-4a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v4a2 2 0 0 1-2 2h-2"></path><path d="M7 14h10v7H7z"></path></svg><?php esc_html_e( 'Print recipe', 'crisp-and-air' ); ?></button>
		</div>
	</header>
	<?php if ( has_post_thumbnail( $post_id ) ) : ?><div class="printable-recipe-card__image"><?php echo get_the_post_thumbnail( $post_id, 'ca-card', array( 'class' => 'ca-image', 'alt' => $thumbnail_alt, 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress image function escapes attributes. ?></div><?php endif; ?>
	<div class="printable-recipe-card__meta">
		<?php if ( $prep ) : ?><div><span><?php esc_html_e( 'Prep', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $prep ) ); ?></strong></div><?php endif; ?>
		<?php if ( $cook ) : ?><div><span><?php esc_html_e( 'Cook', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $cook ) ); ?></strong></div><?php endif; ?>
		<?php if ( $total ) : ?><div><span><?php esc_html_e( 'Total', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_recipe_display_minutes( $total ) ); ?></strong></div><?php endif; ?>
		<?php if ( $servings ) : ?><div><span><?php esc_html_e( 'Yield', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( $servings ); ?></strong></div><?php endif; ?>
		<?php if ( $temp ) : ?><div><span><?php esc_html_e( 'Temperature', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( ca_theme_temperature_label( $temp ) ); ?></strong></div><?php endif; ?>
		<?php if ( $calories ) : ?><div><span><?php esc_html_e( 'Calories', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( number_format_i18n( $calories ) . ' / ' . __( 'serving', 'crisp-and-air' ) ); ?></strong></div><?php endif; ?>
		<?php if ( $course ) : ?><div><span><?php esc_html_e( 'Course', 'crisp-and-air' ); ?></span><strong><?php echo esc_html( $course ); ?></strong></div><?php endif; ?>
	</div>
	<div class="printable-recipe-card__body">
		<?php if ( $ingredients ) : ?>
			<div class="printable-recipe-card__ingredients">
				<h3><?php esc_html_e( 'Ingredients', 'crisp-and-air' ); ?></h3>
				<ul class="ingredient-list"><?php foreach ( $ingredients as $index => $ingredient ) : ?><li><label><input type="checkbox" name="ca-ingredient-<?php echo esc_attr( $post_id ); ?>-<?php echo esc_attr( $index ); ?>" /><span><?php echo esc_html( $ingredient ); ?></span></label></li><?php endforeach; ?></ul>
			</div>
		<?php endif; ?>
		<?php if ( $instructions ) : ?>
			<div class="printable-recipe-card__instructions">
				<h3><?php esc_html_e( 'Instructions', 'crisp-and-air' ); ?></h3>
				<ol class="recipe-steps"><?php foreach ( $instructions as $instruction ) : ?><li><?php echo esc_html( $instruction ); ?></li><?php endforeach; ?></ol>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $notes ) : ?><div class="printable-recipe-card__notes"><strong><?php esc_html_e( 'Notes', 'crisp-and-air' ); ?></strong><p><?php echo nl2br( esc_html( $notes ) ); ?></p></div><?php endif; ?>
	<footer class="printable-recipe-card__footer"><span><?php esc_html_e( 'Recipe from', 'crisp-and-air' ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></span><span><?php echo esc_url( get_permalink( $post_id ) ); ?></span></footer>
</section>
