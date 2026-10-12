<?php
/**
 * Quick action buttons shown under the software title.
 *
 * @package ZabiSoft
 */

$zs_mirrors = zabisoft_get_mirrors();

if ( ! $zs_mirrors ) {
	return;
}
?>
<div class="zs-quick-actions">
	<a class="zs-btn zs-btn--download" href="#zs-download">
		<?php zabisoft_icon( 'download' ); ?>
		<?php esc_html_e( 'Download now', 'zabisoft' ); ?>
	</a>
	<?php
	$zs_external = (string) get_post_meta( get_the_ID(), '_zs_website', true );

	if ( $zs_external && wp_http_validate_url( $zs_external ) ) :
		?>
		<a class="zs-btn zs-btn--ghost" href="<?php echo esc_url( $zs_external ); ?>" target="_blank" rel="noopener nofollow external">
			<?php esc_html_e( 'Official website', 'zabisoft' ); ?>
		</a>
	<?php endif; ?>
	<span class="zs-quick-actions__note">
		<?php
		printf(
			/* translators: %s: number of mirrors. */
			esc_html( _n( '%s mirror', '%s mirrors', count( $zs_mirrors ), 'zabisoft' ) ),
			esc_html( number_format_i18n( count( $zs_mirrors ) ) )
		);
		?>
	</span>
</div>
