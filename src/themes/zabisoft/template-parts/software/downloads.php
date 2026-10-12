<?php
/**
 * Download mirrors box.
 *
 * Mirrors are always plain external links: the theme discloses the destination
 * host, adds rel="nofollow noopener", and never hides the real URL. When link
 * checking is enabled the cached status is shown, and a short countdown gate
 * (same behaviour as classic download pages) can delay the reveal.
 *
 * @package ZabiSoft
 */

$zs_mirrors = zabisoft_get_mirrors();

if ( ! $zs_mirrors ) {
	return;
}

$zs_countdown = (int) get_post_meta( get_the_ID(), '_zs_countdown', true );

if ( ! $zs_countdown ) {
	$zs_countdown = (int) zabisoft_mod( 'countdown_seconds' );
}

if ( ! zabisoft_mod( 'enable_countdown' ) ) {
	$zs_countdown = 0;
}

$zs_button_text = zabisoft_mod( 'download_button_text' );
$zs_button_text = $zs_button_text ? $zs_button_text : __( 'Download', 'zabisoft' );
$zs_gate_id     = 'zs-download-links-' . get_the_ID();
?>
<section id="zs-download" class="zs-downloads">
	<h2 class="zs-downloads__title"><?php esc_html_e( 'Download', 'zabisoft' ); ?></h2>

	<p class="zs-downloads__note">
		<?php
		printf(
			/* translators: %s: number of mirrors. */
			esc_html( _n( 'One download mirror is available for this program.', '%s download mirrors are available for this program.', count( $zs_mirrors ), 'zabisoft' ) ),
			esc_html( number_format_i18n( count( $zs_mirrors ) ) )
		);
		?>
	</p>

	<ul class="zs-mirrors">
		<?php
		foreach ( $zs_mirrors as $zs_index => $zs_mirror ) :
			$zs_host     = zabisoft_url_host( $zs_mirror['url'] );
			$zs_status   = zabisoft_cached_link_status( $zs_mirror['url'] );
			$zs_is_ok    = ! empty( $zs_status['ok'] );
			$zs_checked  = ! empty( $zs_status['time'] );
			$zs_meta     = array_filter(
				array(
					$zs_mirror['platform'],
					$zs_mirror['size'],
					$zs_mirror['type'],
				)
			);
			$zs_show_now = ( 0 === $zs_countdown );
			?>
			<li class="zs-mirror <?php echo $zs_is_ok ? 'zs-mirror--verified' : 'zs-mirror--warning'; ?>">
				<div class="zs-mirror__info">
					<span class="zs-mirror__name"><?php echo esc_html( $zs_mirror['label'] ); ?></span>

					<?php if ( $zs_meta ) : ?>
						<span class="zs-mirror__meta"><?php echo esc_html( implode( ' · ', $zs_meta ) ); ?></span>
					<?php endif; ?>

					<span class="zs-mirror__host"><?php echo esc_html( $zs_host ); ?></span>

					<?php if ( ! $zs_is_ok && $zs_checked ) : ?>
						<span class="zs-mirror__meta zs-mirror__meta--warn">
							<?php
							printf(
								/* translators: %s: date and time of the last check. */
								esc_html__( 'Last automatic check on %s reported a problem with this link.', 'zabisoft' ),
								esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $zs_status['time'] ) )
							);
							?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $zs_mirror['note'] ) ) : ?>
						<span class="zs-mirror__meta"><?php echo esc_html( $zs_mirror['note'] ); ?></span>
					<?php endif; ?>
				</div>

				<div class="zs-mirror__action">
					<?php if ( $zs_show_now ) : ?>
						<a class="zs-btn zs-btn--download"
							href="<?php echo esc_url( $zs_mirror['url'] ); ?>"
							target="_blank"
							rel="<?php echo esc_attr( apply_filters( 'zabisoft_link_rel', 'nofollow noopener external', $zs_mirror['url'], $zs_button_text ) ); ?>"
							data-zs-external
							title="<?php echo esc_attr( sprintf( /* translators: %s: host name. */ __( 'Opens %s in a new tab', 'zabisoft' ), $zs_host ) ); ?>">
							<?php echo esc_html( $zs_button_text ); ?>
						</a>
					<?php else : ?>
						<button type="button"
							class="zs-btn zs-btn--download"
							data-zs-reveal="<?php echo esc_attr( $zs_gate_id ); ?>"
							data-zs-seconds="<?php echo esc_attr( $zs_countdown ); ?>"
							data-zs-url="<?php echo esc_url( $zs_mirror['url'] ); ?>"
							data-zs-host="<?php echo esc_attr( $zs_host ); ?>">
							<?php echo esc_html( $zs_button_text ); ?>
						</button>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $zs_countdown ) : ?>
		<div class="zs-countdown" id="<?php echo esc_attr( $zs_gate_id ); ?>" data-zs-countdown hidden>
			<?php
			printf(
				/* translators: %s: number of seconds. */
				esc_html__( 'Preparing your download link… please wait %s seconds.', 'zabisoft' ),
				'<span data-zs-counter>' . esc_html( $zs_countdown ) . '</span>'
			);
			?>
			<div class="zs-countdown__links" data-zs-links hidden></div>
		</div>
	<?php endif; ?>

	<?php
	$zs_disclosure = zabisoft_mod( 'disclosure_text' );

	if ( $zs_disclosure ) :
		?>
		<div class="zs-disclosure">
			<p><strong><?php esc_html_e( 'Before you download', 'zabisoft' ); ?>:</strong> <?php echo esc_html( $zs_disclosure ); ?></p>
		</div>
	<?php endif; ?>
</section>
