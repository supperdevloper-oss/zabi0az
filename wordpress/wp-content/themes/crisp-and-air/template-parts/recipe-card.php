<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$variant = isset( $args['variant'] ) ? sanitize_html_class( $args['variant'] ) : '';
$terms   = get_the_category( $post_id );
$term    = '';
if ( $terms ) {
	foreach ( $terms as $candidate ) {
		if ( 'air-fryer-recipes' !== $candidate->slug ) {
			$term = $candidate;
			break;
		}
	}
}
$prep = absint( ca_recipe_meta( $post_id, 'prep_minutes' ) );
$cook = absint( ca_recipe_meta( $post_id, 'cook_minutes' ) );
$total = $prep + $cook;
$description = get_the_excerpt( $post_id );
if ( ! $description ) {
	$description = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 17 );
}
$thumbnail_id  = get_post_thumbnail_id( $post_id );
$thumbnail_alt = $thumbnail_id ? get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '';
$thumbnail_alt = $thumbnail_alt ? $thumbnail_alt : get_the_title( $post_id );
?>
<article <?php post_class( array( 'recipe-tile', $variant ? 'recipe-tile--' . $variant : '' ), $post_id ); ?>>
	<a class="recipe-tile__image" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read %s', 'crisp-and-air' ), get_the_title( $post_id ) ) ); ?>">
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $post_id, 'ca-card', array( 'class' => 'ca-image', 'alt' => $thumbnail_alt, 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress image function escapes attributes. ?>
		<?php else : ?>
			<span class="recipe-tile__image-placeholder" aria-hidden="true"><span>✦</span></span>
		<?php endif; ?>
		<span class="recipe-tile__image-arrow" aria-hidden="true">↗</span>
	</a>
	<div class="recipe-tile__body">
		<div class="recipe-tile__eyebrow">
			<?php if ( $term ) : ?><a href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a><?php else : ?><span><?php esc_html_e( 'Air Fryer Recipe', 'crisp-and-air' ); ?></span><?php endif; ?>
			<?php if ( $total ) : ?><span class="recipe-tile__time"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7.5"></circle><path d="M10 5.5v4.8l3 1.8"></path></svg><?php echo esc_html( ca_recipe_display_minutes( $total ) ); ?></span><?php endif; ?>
		</div>
		<h3 class="recipe-tile__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<?php if ( $description ) : ?><p class="recipe-tile__description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>
</article>
