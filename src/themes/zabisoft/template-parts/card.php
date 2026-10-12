<?php
/**
 * Compact card used in grids (related items, archives, widgets).
 *
 * @package ZabiSoft
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-post zs-post--card' ); ?>>
	<div class="zs-card">
		<?php zabisoft_thumbnail( 'zabisoft-card', 'card' ); ?>

		<div class="zs-card__body">
			<h3 class="zs-card__title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h3>

			<?php
			$zs_version = (string) get_post_meta( get_the_ID(), '_zs_version', true );
			if ( '' === $zs_version ) :
				?>
				<p class="zs-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16, '…' ) ); ?></p>
			<?php endif; ?>

			<p class="zs-card__meta">
				<?php echo esc_html( get_the_date() ); ?>
				<?php
				if ( '' !== $zs_version ) {
					echo ' &middot; ' . esc_html( sprintf( /* translators: %s: version number. */ __( 'v%s', 'zabisoft' ), $zs_version ) );
				}
				?>
			</p>
		</div>
	</div>
</article>
