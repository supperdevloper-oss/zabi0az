<?php
/**
 * Installation instructions.
 *
 * @package ZabiSoft
 */

if ( ! zabisoft_mod( 'show_install_steps' ) ) {
	return;
}

$zs_steps = zabisoft_lines_to_list( (string) get_post_meta( get_the_ID(), '_zs_install_steps', true ) );

if ( ! $zs_steps ) {
	return;
}
?>
<section id="zs-install" class="zs-section-block">
	<h2><?php esc_html_e( 'Installation instructions', 'zabisoft' ); ?></h2>
	<p class="zs-section-block__intro"><?php esc_html_e( 'Follow the steps below to install the program on your computer.', 'zabisoft' ); ?></p>

	<ol class="zs-install-steps">
		<?php foreach ( $zs_steps as $zs_step ) : ?>
			<li><?php echo esc_html( $zs_step ); ?></li>
		<?php endforeach; ?>
	</ol>
</section>
