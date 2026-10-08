<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'crisp-and-air' ); ?></a>
<div class="announcement-bar"><span class="announcement-bar__spark" aria-hidden="true">✦</span> <?php esc_html_e( 'Good food. Less fuss. More crisp.', 'crisp-and-air' ); ?></div>
<header class="site-header">
	<div class="site-header__inner wrap">
		<div class="brand-lockup">
			<?php if ( has_custom_logo() ) : ?>
				<div class="brand-lockup__image"><?php the_custom_logo(); ?></div>
			<?php else : ?>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — Home' ); ?>">
					<span class="brand__mark" aria-hidden="true"><span></span><span></span><span></span><span></span></span>
					<span class="brand__words"><span class="brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'Crisp & Air' ); ?></span><span class="brand__tagline"><?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'The air fryer table', 'crisp-and-air' ) ); ?></span></span>
				</a>
			<?php endif; ?>
		</div>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span></span>
			<span class="menu-toggle__label"><?php esc_html_e( 'Menu', 'crisp-and-air' ); ?></span>
		</button>
		<nav class="primary-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'crisp-and-air' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-nav__list',
					'fallback_cb'    => 'ca_theme_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<div class="site-header__actions">
			<button class="search-toggle" type="button" aria-expanded="false" aria-controls="site-search-panel" aria-label="<?php esc_attr_e( 'Open recipe search', 'crisp-and-air' ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.7"></circle><path d="m16 16 4.3 4.3"></path></svg>
			</button>
			<a class="header-recipes-link" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse recipes', 'crisp-and-air' ); ?><span aria-hidden="true"> ↗</span></a>
		</div>
	</div>
	<div class="site-search-panel" id="site-search-panel">
		<div class="wrap site-search-panel__inner">
			<?php ca_theme_search_form( 'ca-search-form--header' ); ?>
			<p><?php esc_html_e( 'Try a dish, ingredient, or category.', 'crisp-and-air' ); ?></p>
		</div>
	</div>
	<noscript><div class="wrap site-search-noscript"><?php ca_theme_search_form(); ?></div></noscript>
</header>
