<?php
/**
 * “Nothing found” message.
 *
 * @package ZabiSoft
 */

?>
<div class="zs-no-results">
	<h2 class="zs-404__title">
		<?php
		if ( is_search() ) {
			esc_html_e( 'Nothing matched your search', 'zabisoft' );
		} else {
			esc_html_e( 'No content yet', 'zabisoft' );
		}
		?>
	</h2>

	<?php if ( is_search() ) : ?>
		<p class="zs-404__text"><?php esc_html_e( 'Try a shorter keyword, or search for the name of the program you need.', 'zabisoft' ); ?></p>
	<?php else : ?>
		<p class="zs-404__text"><?php esc_html_e( 'There is nothing to show here yet. Add your first entry from the WordPress dashboard.', 'zabisoft' ); ?></p>
	<?php endif; ?>

	<div class="zs-404__search"><?php get_search_form(); ?></div>
</div>
