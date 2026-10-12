<?php
/**
 * Site footer.
 *
 * @package ZabiSoft
 */

?>
			</main><!-- #main -->

			<?php get_sidebar(); ?>
		</div><!-- .zs-layout -->
	</div><!-- .zs-container -->

	<footer id="colophon" class="zs-footer">
		<?php
		$zs_columns = (int) zabisoft_mod( 'footer_columns' );
		$zs_active  = array();

		for ( $zs_i = 1; $zs_i <= $zs_columns; $zs_i++ ) {
			if ( is_active_sidebar( 'footer-' . $zs_i ) ) {
				$zs_active[] = $zs_i;
			}
		}

		if ( $zs_active ) :
			?>
			<div class="zs-container">
				<div class="zs-footer__widgets zs-cols-<?php echo esc_attr( count( $zs_active ) ); ?>">
					<?php foreach ( $zs_active as $zs_i ) : ?>
						<div class="zs-footer__col"><?php dynamic_sidebar( 'footer-' . $zs_i ); ?></div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="zs-container">
			<?php
			if ( zabisoft_has_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'zs-footer__menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>
		</div>

		<div class="zs-copyright">
			<div class="zs-container">
				<?php
				$zs_footer_text = zabisoft_mod( 'footer_text' );

				if ( $zs_footer_text ) {
					echo wp_kses_post( wpautop( $zs_footer_text ) );
				} else {
					printf(
						'<p>&copy; %1$s <a href="%2$s">%3$s</a>. %4$s</p>',
						esc_html( gmdate( 'Y' ) ),
						esc_url( home_url( '/' ) ),
						esc_html( get_bloginfo( 'name' ) ),
						esc_html__( 'All rights reserved.', 'zabisoft' )
					);
				}
				?>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php if ( zabisoft_mod( 'back_to_top' ) ) : ?>
	<a class="zs-go-top" href="#page" data-zs-go-top>
		<?php zabisoft_icon( 'arrow-up' ); ?>
		<?php esc_html_e( 'Go to top', 'zabisoft' ); ?>
	</a>
<?php endif; ?>

<?php
echo zabisoft_icon_symbols(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.

wp_footer();
?>
</body>
</html>
