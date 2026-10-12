<?php
/**
 * ZabiSoft theme bootstrap.
 *
 * @package ZabiSoft
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'ZABISOFT_VERSION', '1.0.0' );
define( 'ZABISOFT_DIR', trailingslashit( get_template_directory() ) );
define( 'ZABISOFT_URI', trailingslashit( get_template_directory_uri() ) );

/**
 * Theme setup.
 *
 * @return void
 */
function zabisoft_setup() {
	load_theme_textdomain( 'zabisoft', ZABISOFT_DIR . 'languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ececec',
		)
	);

	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu (below the header)', 'zabisoft' ),
			'topbar'  => __( 'Top bar menu', 'zabisoft' ),
			'footer'  => __( 'Footer links', 'zabisoft' ),
		)
	);

	// Thumbnail sizes used across the theme.
	set_post_thumbnail_size( 400, 300, true );
	add_image_size( 'zabisoft-thumb', 400, 300, true );
	add_image_size( 'zabisoft-card', 520, 325, true );
	add_image_size( 'zabisoft-wide', 900, 560, true );
}
add_action( 'after_setup_theme', 'zabisoft_setup' );

/**
 * Set the content width in pixels.
 *
 * @return void
 */
function zabisoft_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'zabisoft_content_width', 780 );
}
add_action( 'after_setup_theme', 'zabisoft_content_width', 0 );

/**
 * Register widget areas.
 *
 * @return void
 */
function zabisoft_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Main sidebar', 'zabisoft' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Widgets shown next to posts, archives and the front page.', 'zabisoft' ),
			'before_widget' => '<section id="%1$s" class="zs-widget widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="zs-widget__title widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Front page sidebar', 'zabisoft' ),
			'id'            => 'front-page',
			'description'   => __( 'Optional widget area used only on the front page. Leave empty to reuse the main sidebar.', 'zabisoft' ),
			'before_widget' => '<section id="%1$s" class="zs-widget widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="zs-widget__title widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Footer column %d', 'zabisoft' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Widgetised footer column.', 'zabisoft' ),
				'before_widget' => '<section id="%1$s" class="zs-widget widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="zs-widget__title widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'zabisoft_widgets_init' );

/**
 * Get a theme option with the registered default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function zabisoft_mod( $key ) {
	$defaults = zabisoft_customizer_defaults();

	$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( 'zabisoft_' . $key, $default );
}

/**
 * Front-end assets.
 *
 * @return void
 */
function zabisoft_scripts() {
	wp_enqueue_style( 'zabisoft-style', get_stylesheet_uri(), array(), ZABISOFT_VERSION );
	wp_style_add_data( 'zabisoft-style', 'rtl', 'replace' );

	$features = array();
	if ( (int) zabisoft_mod( 'enable_countdown' ) ) {
		$features[] = 'countdown';
	}

	wp_enqueue_script( 'zabisoft-script', ZABISOFT_URI . 'assets/js/zabisoft.js', array(), ZABISOFT_VERSION, true );
	wp_localize_script(
		'zabisoft-script',
		'zabisoftData',
		array(
			'countdown'   => (int) zabisoft_mod( 'countdown_seconds' ),
			'waitText'    => __( 'Please wait %s seconds…', 'zabisoft' ),
			'readyText'   => __( 'Your download links are ready below.', 'zabisoft' ),
			'revealText'  => __( 'Reveal download links', 'zabisoft' ),
			'verifyLinks' => (bool) zabisoft_mod( 'verify_links' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zabisoft_scripts' );

/**
 * Remove the emoji detection script when the option is enabled.
 *
 * @return void
 */
function zabisoft_optimise_head() {
	if ( ! zabisoft_mod( 'disable_emoji' ) ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'zabisoft_optimise_head' );

/**
 * Add informative body classes.
 *
 * @param array $classes Existing classes.
 * @return array
 */
function zabisoft_body_classes( $classes ) {
	$sidebar = zabisoft_mod( 'sidebar_position' );

	if ( is_page_template( 'page-templates/template-full-width.php' ) ) {
		$sidebar = 'none';
	}

	if ( 'none' === $sidebar ) {
		$classes[] = 'zs-no-sidebar';
	} elseif ( 'left' === $sidebar ) {
		$classes[] = 'zs-sidebar-left';
	}

	$classes[] = 'zs-scheme-' . sanitize_html_class( zabisoft_mod( 'color_scheme' ) );

	if ( is_singular( 'software' ) ) {
		$classes[] = 'zs-single-software';
	}

	if ( ! is_active_sidebar( 'sidebar-1' ) && ! is_active_sidebar( 'front-page' ) ) {
		$classes[] = 'zs-no-widgets';
	}

	return $classes;
}
add_filter( 'body_class', 'zabisoft_body_classes' );

/**
 * Load the theme includes.
 *
 * @return void
 */
function zabisoft_includes() {
	require_once ZABISOFT_DIR . 'inc/template-tags.php';
	require_once ZABISOFT_DIR . 'inc/comments.php';
	require_once ZABISOFT_DIR . 'inc/class-zabisoft-nav-walker.php';
	require_once ZABISOFT_DIR . 'inc/customizer.php';
	require_once ZABISOFT_DIR . 'inc/widgets.php';
	require_once ZABISOFT_DIR . 'inc/software.php';
	require_once ZABISOFT_DIR . 'inc/schema.php';
	require_once ZABISOFT_DIR . 'inc/seo.php';
	require_once ZABISOFT_DIR . 'inc/contact-form.php';
	require_once ZABISOFT_DIR . 'inc/editor.php';
	require_once ZABISOFT_DIR . 'inc/admin.php';
	require_once ZABISOFT_DIR . 'inc/software-fallback.php';
}
zabisoft_includes();

/**
 * Nicer excerpt more string.
 *
 * @return string
 */
function zabisoft_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'zabisoft_excerpt_more' );

/**
 * Excerpt length used in listings.
 *
 * @param int $length Default length.
 * @return int
 */
function zabisoft_excerpt_length( $length ) {
	return 34;
}
add_filter( 'excerpt_length', 'zabisoft_excerpt_length', 20 );

/**
 * Allow shortcodes inside excerpts and widgets text.
 *
 * @return void
 */
function zabisoft_excerpt_filters() {
	add_filter( 'the_excerpt', 'do_shortcode' );
	add_filter( 'widget_text', 'do_shortcode' );
}
add_action( 'init', 'zabisoft_excerpt_filters' );

/**
 * Pingback header for singular pages.
 *
 * @return void
 */
function zabisoft_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'zabisoft_pingback_header' );

/**
 * Custom page templates for this theme are discoverable in the admin.
 *
 * @param array $templates Templates.
 * @return array
 */
function zabisoft_page_template_labels( $templates ) {
	return $templates;
}
add_filter( 'theme_page_templates', 'zabisoft_page_template_labels' );
