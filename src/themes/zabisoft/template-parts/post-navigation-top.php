<?php
/**
 * Previous / next post links, shown above the entry title.
 *
 * @package ZabiSoft
 */

$zs_prev = get_previous_post();
$zs_next = get_next_post();

if ( ! $zs_prev && ! $zs_next ) {
	return;
}
?>
<nav class="zs-post-nav zs-post-nav--top" aria-label="<?php esc_attr_e( 'Post navigation', 'zabisoft' ); ?>">
	<?php if ( $zs_prev ) : ?>
		<a class="prev" href="<?php echo esc_url( get_permalink( $zs_prev ) ); ?>">
			<span class="zs-post-nav__label"><?php esc_html_e( 'Previous', 'zabisoft' ); ?></span>
			<?php echo esc_html( wp_trim_words( get_the_title( $zs_prev ), 10, '…' ) ); ?>
		</a>
	<?php endif; ?>

	<?php if ( $zs_next ) : ?>
		<a class="next" href="<?php echo esc_url( get_permalink( $zs_next ) ); ?>">
			<span class="zs-post-nav__label"><?php esc_html_e( 'Next', 'zabisoft' ); ?></span>
			<?php echo esc_html( wp_trim_words( get_the_title( $zs_next ), 10, '…' ) ); ?>
		</a>
	<?php endif; ?>
</nav>
