<?php
/**
 * Sidebar.
 *
 * @package ZabiSoft
 */

if ( ! zabisoft_has_sidebar() ) {
	return;
}

$zs_sidebar_id = 'sidebar-1';

if ( is_front_page() && ! zabisoft_mod( 'front_sidebar_shared' ) && is_active_sidebar( 'front-page' ) ) {
	$zs_sidebar_id = 'front-page';
}
?>
<aside id="secondary" class="zs-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'zabisoft' ); ?>">
	<?php dynamic_sidebar( $zs_sidebar_id ); ?>
</aside>
