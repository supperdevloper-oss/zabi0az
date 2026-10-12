<?php
/**
 * Key features list.
 *
 * @package ZabiSoft
 */

$zs_features = zabisoft_lines_to_list( (string) get_post_meta( get_the_ID(), '_zs_features', true ) );

if ( ! $zs_features ) {
	return;
}
?>
<section id="zs-features" class="zs-section-block">
	<h2><?php esc_html_e( 'Key features', 'zabisoft' ); ?></h2>

	<ul class="zs-features">
		<?php foreach ( $zs_features as $zs_feature ) : ?>
			<li><?php echo esc_html( $zs_feature ); ?></li>
		<?php endforeach; ?>
	</ul>
</section>
