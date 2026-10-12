<?php
/**
 * Customizer options.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central list of option defaults.
 *
 * @return array
 */
function zabisoft_customizer_defaults() {
	return array(
		// Design.
		'container_width'      => 1140,
		'sidebar_position'     => 'right',      // right | left | none.
		'color_scheme'         => 'green',      // green | blue | red | orange | violet.
		'show_topbar'          => 1,
		'topbar_text'          => '',
		'header_search'        => 1,
		'show_social'          => 1,
		'social_facebook'      => '',
		'social_twitter'       => '',
		'social_youtube'       => '',
		'social_telegram'      => '',
		'social_github'        => '',

		// Front page.
		'front_hero'           => 1,
		'front_hero_title'     => __( 'Download Free Your Desired Software', 'zabisoft' ),
		'front_hero_text'      => __( 'Hand picked installers, portable tools, drivers and operating systems — with version, file size, system requirements and verified mirrors on every page.', 'zabisoft' ),
		'front_hero_search'    => 1,
		'front_quick_links'    => '',
		'front_category_strip' => 1,
		'front_sections'       => 1,
		'front_section_title'  => __( 'Latest software & tutorials', 'zabisoft' ),
		'front_posts_option'   => 'merged',     // merged | software | posts.
		'front_posts_count'    => 10,
		'front_sidebar_shared' => 1,

		// Software pages.
		'enable_countdown'     => 1,
		'countdown_seconds'    => 10,
		'download_button_text' => __( 'Download', 'zabisoft' ),
		'show_specs'           => 1,
		'show_requirements'    => 1,
		'show_screenshots'     => 1,
		'show_install_steps'   => 1,
		'show_toc'             => 1,
		'verify_links'         => 1,
		'disclosure_text'      => __( 'Download buttons open an external website you did not reach from this page. Always check the publisher and licence before installing anything.', 'zabisoft' ),

		// Footer.
		'footer_text'          => '',
		'footer_columns'       => 4,
		'back_to_top'          => 1,

		// Performance.
		'disable_emoji'        => 1,
	);
}

/**
 * Register customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 * @return void
 */
function zabisoft_customize_register( $wp_customize ) {
	$defaults = zabisoft_customizer_defaults();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/**
	 * Helper to register a setting + control quickly.
	 *
	 * @param string $id      Setting id without prefix.
	 * @param string $section Section id.
	 * @param string $label   Control label.
	 * @param string $type    Control type.
	 * @param array  $args    Extra control args.
	 * @return void
	 */
	$add = function ( $id, $section, $label, $type = 'text', $args = array() ) use ( $wp_customize, $defaults ) {
		$setting_id = 'zabisoft_' . $id;

		$sanitize = isset( $args['sanitize_callback'] ) ? $args['sanitize_callback'] : 'sanitize_text_field';
		unset( $args['sanitize_callback'] );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => isset( $defaults[ $id ] ) ? $defaults[ $id ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);

		if ( 'checkbox' === $type ) {
			$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'checkbox' ), $args ) );

			return;
		}

		if ( 'select' === $type ) {
			$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'select' ), $args ) );

			return;
		}

		if ( 'number' === $type ) {
			$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'number' ), $args ) );

			return;
		}

		if ( 'textarea' === $type ) {
			$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'textarea' ), $args ) );

			return;
		}

		if ( 'url' === $type ) {
			$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'url' ), $args ) );

			return;
		}

		$wp_customize->add_control( $setting_id, array_merge( array( 'section' => $section, 'label' => $label, 'type' => 'text' ), $args ) );
	};

	// Panels -----------------------------------------------------------------.
	$wp_customize->add_panel(
		'zabisoft_panel',
		array(
			'title'       => __( 'ZabiSoft theme options', 'zabisoft' ),
			'description' => __( 'Layout, colours, front page, software pages and footer settings.', 'zabisoft' ),
			'priority'    => 10,
		)
	);

	$sections = array(
		'zabisoft_design'     => __( 'Layout & colours', 'zabisoft' ),
		'zabisoft_front'      => __( 'Front page', 'zabisoft' ),
		'zabisoft_software'   => __( 'Software pages', 'zabisoft' ),
		'zabisoft_footer'     => __( 'Footer & social', 'zabisoft' ),
		'zabisoft_performance' => __( 'Performance', 'zabisoft' ),
	);

	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'zabisoft_panel',
				'priority' => 10,
			)
		);
	}

	// Layout & colours -------------------------------------------------------.
	$add(
		'container_width',
		'zabisoft_design',
		__( 'Container width (px)', 'zabisoft' ),
		'select',
		array(
			'choices'           => array(
				980  => '980 px',
				1080 => '1080 px',
				1140 => '1140 px (default)',
				1200 => '1200 px',
				1400 => '1400 px',
			),
			'sanitize_callback' => 'absint',
		)
	);

	$add(
		'sidebar_position',
		'zabisoft_design',
		__( 'Sidebar position', 'zabisoft' ),
		'select',
		array(
			'choices'           => array(
				'right' => __( 'Right (default)', 'zabisoft' ),
				'left'  => __( 'Left', 'zabisoft' ),
				'none'  => __( 'No sidebar (full width content)', 'zabisoft' ),
			),
			'sanitize_callback' => 'sanitize_key',
		)
	);

	$add(
		'color_scheme',
		'zabisoft_design',
		__( 'Accent colour scheme', 'zabisoft' ),
		'select',
		array(
			'choices'           => array(
				'green'  => __( 'Green (theme default)', 'zabisoft' ),
				'blue'   => __( 'Blue', 'zabisoft' ),
				'red'    => __( 'Red', 'zabisoft' ),
				'orange' => __( 'Orange', 'zabisoft' ),
				'violet' => __( 'Violet', 'zabisoft' ),
			),
			'sanitize_callback' => 'sanitize_key',
		)
	);

	$add( 'show_topbar', 'zabisoft_design', __( 'Show the black top bar', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'topbar_text', 'zabisoft_design', __( 'Top bar right side text', 'zabisoft' ), 'text', array( 'description' => __( 'Example: “Updated daily · Verified mirrors”. Leave empty to hide.', 'zabisoft' ) ) );
	$add( 'header_search', 'zabisoft_design', __( 'Show the search box in the header', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'show_social', 'zabisoft_design', __( 'Show social icons in the header', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );

	// Front page -------------------------------------------------------------.
	$add( 'front_hero', 'zabisoft_front', __( 'Show the hero banner on the front page', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'front_hero_title', 'zabisoft_front', __( 'Hero title', 'zabisoft' ), 'text' );
	$add( 'front_hero_text', 'zabisoft_front', __( 'Hero description', 'zabisoft' ), 'textarea', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$add( 'front_hero_search', 'zabisoft_front', __( 'Show the big search form in the hero', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'front_quick_links', 'zabisoft_front', __( 'Popular search suggestions', 'zabisoft' ), 'text', array( 'description' => __( 'Comma separated keywords shown as quick links, e.g. Office, Antivirus, Drivers.', 'zabisoft' ) ) );
	$add( 'front_category_strip', 'zabisoft_front', __( 'Show the category strip', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'front_sections', 'zabisoft_front', __( 'Show the section heading above the list', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'front_section_title', 'zabisoft_front', __( 'Section heading text', 'zabisoft' ), 'text' );
	$add(
		'front_posts_option',
		'zabisoft_front',
		__( 'What to list on the front page', 'zabisoft' ),
		'select',
		array(
			'choices'           => array(
				'merged'   => __( 'Software + blog posts (default)', 'zabisoft' ),
				'software' => __( 'Only software entries', 'zabisoft' ),
				'posts'    => __( 'Only blog posts', 'zabisoft' ),
			),
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$add( 'front_posts_count', 'zabisoft_front', __( 'Number of entries on the front page', 'zabisoft' ), 'number', array( 'sanitize_callback' => 'absint', 'input_attrs' => array( 'min' => 1, 'max' => 30 ) ) );
	$add( 'front_sidebar_shared', 'zabisoft_front', __( 'Use the main sidebar on the front page too', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );

	// Software ---------------------------------------------------------------.
	$add( 'enable_countdown', 'zabisoft_software', __( 'Require a short countdown before revealing download links', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'countdown_seconds', 'zabisoft_software', __( 'Countdown length (seconds)', 'zabisoft' ), 'number', array( 'sanitize_callback' => 'absint', 'input_attrs' => array( 'min' => 0, 'max' => 60 ) ) );
	$add( 'download_button_text', 'zabisoft_software', __( 'Download button label', 'zabisoft' ), 'text' );
	$add( 'show_specs', 'zabisoft_software', __( 'Show the “Technical setup details” table', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'show_requirements', 'zabisoft_software', __( 'Show the system requirements table', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'show_screenshots', 'zabisoft_software', __( 'Show the screenshot gallery', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'show_install_steps', 'zabisoft_software', __( 'Show installation instructions', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'show_toc', 'zabisoft_software', __( 'Show the automatic table of contents', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
	$add( 'verify_links', 'zabisoft_software', __( 'Check download URLs before printing them', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox', 'description' => __( 'Only external http(s) links are checked, once per hour, and cached.', 'zabisoft' ) ) );
	$add( 'disclosure_text', 'zabisoft_software', __( 'External download disclosure', 'zabisoft' ), 'textarea', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );

	// Footer -----------------------------------------------------------------.
	$add( 'footer_text', 'zabisoft_footer', __( 'Copyright text', 'zabisoft' ), 'textarea', array( 'sanitize_callback' => 'wp_kses_post', 'description' => __( 'Leave empty to print “© YEAR Site name”. Basic HTML is allowed.', 'zabisoft' ) ) );
	$add( 'footer_columns', 'zabisoft_footer', __( 'Footer columns', 'zabisoft' ), 'select', array( 'choices' => array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ), 'sanitize_callback' => 'absint' ) );
	$add( 'back_to_top', 'zabisoft_footer', __( 'Show the “Go to top” button', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );

	$socials = array(
		'facebook' => __( 'Facebook URL', 'zabisoft' ),
		'twitter'  => __( 'X / Twitter URL', 'zabisoft' ),
		'youtube'  => __( 'YouTube URL', 'zabisoft' ),
		'telegram' => __( 'Telegram URL', 'zabisoft' ),
		'github'   => __( 'GitHub URL', 'zabisoft' ),
	);

	foreach ( $socials as $key => $label ) {
		$add(
			'social_' . $key,
			'zabisoft_footer',
			$label,
			'url',
			array( 'sanitize_callback' => 'esc_url_raw' )
		);
	}

	// Performance ------------------------------------------------------------.
	$add( 'disable_emoji', 'zabisoft_performance', __( 'Remove the WordPress emoji script and styles', 'zabisoft' ), 'checkbox', array( 'sanitize_callback' => 'zabisoft_sanitize_checkbox' ) );
}
add_action( 'customize_register', 'zabisoft_customize_register' );

/**
 * Sanitise checkbox values.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function zabisoft_sanitize_checkbox( $value ) {
	return ( isset( $value ) && ( true === $value || 1 === (int) $value || '1' === $value || 'on' === $value || 'true' === $value ) ) ? 1 : 0;
}

/**
 * Live preview script for the customizer.
 *
 * @return void
 */
function zabisoft_customize_preview_js() {
	wp_enqueue_script( 'zabisoft-customizer', ZABISOFT_URI . 'assets/js/customizer.js', array( 'customize-preview' ), ZABISOFT_VERSION, true );
}
add_action( 'customize_preview_init', 'zabisoft_customize_preview_js' );
