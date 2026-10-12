<?php
/**
 * Site header.
 *
 * @package ZabiSoft
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="zs-skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'zabisoft' ); ?></a>

<div id="page" class="zs-site">

	<?php if ( zabisoft_mod( 'show_topbar' ) ) : ?>
		<div class="zs-topbar">
			<div class="zs-container">
				<?php if ( has_nav_menu( 'topbar' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'topbar',
							'container'      => false,
							'menu_class'     => 'zs-topbar__menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				<?php else : ?>
					<span class="zs-topbar__meta"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
				<?php endif; ?>

				<?php
				$zs_topbar_text = zabisoft_mod( 'topbar_text' );
				if ( $zs_topbar_text ) :
					?>
					<span class="zs-topbar__meta"><?php echo esc_html( $zs_topbar_text ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<header id="masthead" class="zs-header">
		<div class="zs-container zs-header__inner">
			<div class="zs-branding">
				<?php if ( has_custom_logo() ) : ?>
					<div class="zs-branding__logo"><?php the_custom_logo(); ?></div>
				<?php endif; ?>

				<div class="zs-branding__text">
					<?php if ( is_front_page() && is_home() ) : ?>
						<p class="zs-site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php else : ?>
						<p class="zs-site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php endif; ?>

					<?php
					$zs_description = get_bloginfo( 'description', 'display' );
					if ( $zs_description || is_customize_preview() ) :
						?>
						<p class="zs-site-description"><?php echo esc_html( $zs_description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="zs-header__aside">
				<?php if ( zabisoft_mod( 'header_search' ) ) : ?>
					<div class="zs-header__search"><?php get_search_form(); ?></div>
				<?php endif; ?>

				<?php if ( zabisoft_mod( 'show_social' ) ) : ?>
					<?php $zs_socials = zabisoft_social_links(); ?>
					<?php if ( $zs_socials ) : ?>
						<ul class="zs-social">
							<?php foreach ( $zs_socials as $zs_key => $zs_social ) : ?>
								<li>
									<a href="<?php echo esc_url( $zs_social['url'] ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( $zs_social['label'] ); ?>">
										<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="<?php echo esc_attr( $zs_social['icon'] ); ?>"/></svg>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<nav id="site-navigation" class="zs-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'zabisoft' ); ?>">
		<div class="zs-container">
			<button class="zs-nav__toggle" type="button" aria-expanded="false" aria-controls="zs-primary-menu">
				<?php zabisoft_icon( 'menu' ); ?>
				<?php esc_html_e( 'Browse categories', 'zabisoft' ); ?>
			</button>

			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_id'        => 'zs-primary-menu',
						'menu_class'     => 'zs-nav__list',
						'walker'         => new ZabiSoft_Nav_Walker(),
						'depth'          => 3,
						'fallback_cb'    => false,
					)
				);
			} else {
				zabisoft_fallback_menu();
			}
			?>
		</div>
	</nav>

	<div class="zs-container">
		<div class="zs-layout">
			<main id="main" class="zs-content">
