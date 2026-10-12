<?php
/**
 * System requirements table.
 *
 * @package ZabiSoft
 */

if ( ! zabisoft_mod( 'show_requirements' ) ) {
	return;
}

$zs_rows = zabisoft_requirement_rows();

if ( ! $zs_rows ) {
	return;
}
?>
<section id="zs-requirements" class="zs-section-block">
	<h2><?php esc_html_e( 'System requirements', 'zabisoft' ); ?></h2>
	<p class="zs-section-block__intro"><?php esc_html_e( 'Make sure your computer meets these specifications before installing the program.', 'zabisoft' ); ?></p>

	<div class="zs-software-box zs-software-box--requirements">
		<table class="zs-specs">
			<tbody>
				<?php foreach ( $zs_rows as $zs_label => $zs_value ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $zs_label ); ?></th>
						<td><?php echo esc_html( $zs_value ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
