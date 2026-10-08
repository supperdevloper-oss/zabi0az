<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="site-footer" id="site-footer">
	<div class="site-footer__main wrap">
		<div class="footer-brand-block" id="about">
			<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'Crisp & Air' ); ?><span aria-hidden="true">.</span></a>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'Air fryer recipes for everyday cooking.', 'crisp-and-air' ) ); ?></p>
			<p class="footer-brand-block__about"><?php esc_html_e( 'A thoughtful collection of crisp, practical air-fryer recipes, made to help you decide what to cook next.', 'crisp-and-air' ); ?></p>
			<div class="footer-social">
				<?php if ( get_theme_mod( 'ca_social_instagram' ) ) : ?><a href="<?php echo esc_url( get_theme_mod( 'ca_social_instagram' ) ); ?>" rel="me noopener noreferrer" target="_blank">Instagram <span aria-hidden="true">↗</span></a><?php endif; ?>
				<?php if ( get_theme_mod( 'ca_social_pinterest' ) ) : ?><a href="<?php echo esc_url( get_theme_mod( 'ca_social_pinterest' ) ); ?>" rel="me noopener noreferrer" target="_blank">Pinterest <span aria-hidden="true">↗</span></a><?php endif; ?>
			</div>
		</div>
		<div class="footer-column">
			<h2><?php esc_html_e( 'Explore', 'crisp-and-air' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-nav__list',
					'fallback_cb'    => 'ca_theme_footer_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</div>
		<div class="footer-column footer-categories">
			<h2><?php esc_html_e( 'Find a recipe', 'crisp-and-air' ); ?></h2>
			<ul>
				<?php
			$footer_terms = ca_theme_category_terms( 6 );
			foreach ( $footer_terms as $footer_term ) :
				?>
				<li><a href="<?php echo esc_url( get_category_link( $footer_term ) ); ?>"><?php echo esc_html( $footer_term->name ); ?></a></li>
			<?php endforeach; ?>
				<li><a href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'All categories', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></li>
			</ul>
		</div>
		<div class="footer-column footer-note">
			<h2><?php esc_html_e( 'Cook with confidence', 'crisp-and-air' ); ?></h2>
			<p><?php esc_html_e( 'Air-fryer times vary by appliance and food size. Follow the recipe’s doneness guidance and check food safety where it matters.', 'crisp-and-air' ); ?></p>
			<a class="footer-note__link" href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>"><?php esc_html_e( 'Read our cooking disclaimer', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a>
		</div>
	</div>
	<?php if ( is_active_sidebar( 'ca-footer' ) ) : ?><div class="wrap footer-widgets"><?php dynamic_sidebar( 'ca-footer' ); ?></div><?php endif; ?>
	<div class="site-footer__bottom">
		<div class="wrap site-footer__bottom-inner">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ?: 'Crisp & Air' ); ?>. <?php esc_html_e( 'All rights reserved.', 'crisp-and-air' ); ?></p>
			<p class="site-footer__made"><?php esc_html_e( 'Made for good food and less fuss.', 'crisp-and-air' ); ?></p>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
