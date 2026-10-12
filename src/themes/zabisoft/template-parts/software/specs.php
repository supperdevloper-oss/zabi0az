<?php
/**
 * Technical setup details table.
 *
 * @package ZabiSoft
 */

if ( ! zabisoft_mod( 'show_specs' ) ) {
	return;
}

$zs_rows = zabisoft_specs_rows();

if ( ! $zs_rows ) {
	return;
}
?>
<section id="zs-specs" class="zs-section-block">
	<h2><?php esc_html_e( 'Technical setup details', 'zabisoft' ); ?></h2>

	<div class="zs-software-box">
		<div class="zs-software-box__head">
			<p class="zs-software-box__name"><?php echo esc_html( get_the_title() ); ?></p>
			<?php
			$zs_version = (string) get_post_meta( get_the_ID(), '_zs_version', true );
			if ( '' !== $zs_version ) :
				?>
				<span class="zs-software-box__version"><?php echo esc_html( sprintf( /* translators: %s: version. */ __( 'Version %s', 'zabisoft' ), $zs_version ) ); ?></span>
			<?php endif; ?>
		</div>

		<table class="zs-specs">
			<tbody>
				<?php foreach ( $zs_rows as $zs_label => $zs_value ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $zs_label ); ?></th>
						<td>
							<?php
							if ( '_zs_website' === array_search( $zs_label, zabisoft_software_fields(), true ) && wp_http_validate_url( $zs_value ) ) {
								printf(
									'<a href="%1$s" target="_blank" rel="noopener nofollow external">%2$s</a>',
									esc_url( $zs_value ),
									esc_html( zabisoft_url_host( $zs_value ) ? zabisoft_url_host( $zs_value ) : $zs_value )
								);
							} else {
								echo esc_html( $zs_value );
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
