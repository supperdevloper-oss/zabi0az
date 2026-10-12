<?php
/**
 * Screenshot gallery.
 *
 * @package ZabiSoft
 */

$zs_shots = get_post_meta( get_the_ID(), '_zs_screenshots', true );

if ( ! is_array( $zs_shots ) || empty( $zs_shots ) ) {
	return;
}

$zs_shots = array_filter( array_map( 'absint', $zs_shots ) );

if ( ! $zs_shots ) {
	return;
}
?>
<section id="zs-screenshots" class="zs-section-block">
	<h2><?php esc_html_e( 'Screenshots', 'zabisoft' ); ?></h2>

	<ul class="zs-screenshots">
		<?php foreach ( $zs_shots as $zs_id ) : ?>
			<?php
			$zs_full = wp_get_attachment_image_src( $zs_id, 'large' );

			if ( ! $zs_full ) {
				continue;
			}
			?>
			<li>
				<a href="<?php echo esc_url( $zs_full[0] ); ?>" class="zs-screenshot" data-zs-lightbox>
					<?php
					echo wp_get_attachment_image(
						$zs_id,
						'zabisoft-card',
						false,
						array(
							'loading' => 'lazy',
							'alt'     => esc_attr( get_post_meta( $zs_id, '_wp_attachment_image_alt', true ) ),
						)
					);
					?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
