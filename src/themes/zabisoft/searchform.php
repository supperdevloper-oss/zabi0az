<?php
/**
 * Search form.
 *
 * @package ZabiSoft
 */

$zs_unique_id = 'zs-search-' . wp_unique_id();

$zs_placeholder = isset( $args['zs_placeholder'] ) ? $args['zs_placeholder'] : __( 'Search…', 'zabisoft' );
?>
<form role="search" method="get" class="zs-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $zs_unique_id ); ?>"><?php esc_html_e( 'Search for:', 'zabisoft' ); ?></label>
	<input
		type="search"
		id="<?php echo esc_attr( $zs_unique_id ); ?>"
		class="zs-search-field"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
		placeholder="<?php echo esc_attr( $zs_placeholder ); ?>"
		required
	>
	<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'zabisoft' ); ?>">
		<?php zabisoft_icon( 'search' ); ?>
		<span class="zs-search-label"><?php esc_html_e( 'Search', 'zabisoft' ); ?></span>
	</button>
</form>
