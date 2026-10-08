<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CA_THEME_VERSION', '1.0.0' );

// Keep already-saved recipe details readable if the companion plugin is temporarily disabled.
if ( ! function_exists( 'ca_recipe_meta' ) ) {
	function ca_recipe_meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( (int) $post_id, '_ca_' . sanitize_key( $key ), true );
		return '' === $value || null === $value ? $default : $value;
	}
}
if ( ! function_exists( 'ca_recipe_lines' ) ) {
	function ca_recipe_lines( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
		return array_values( array_filter( array_map( 'trim', array_map( 'wp_strip_all_tags', $lines ) ) ) );
	}
}
if ( ! function_exists( 'ca_recipe_faq_items' ) ) {
	function ca_recipe_faq_items( $value ) {
		$items = array();
		foreach ( ca_recipe_lines( $value ) as $line ) {
			$parts = explode( '|', $line, 2 );
			if ( 2 === count( $parts ) && trim( $parts[0] ) && trim( $parts[1] ) ) {
				$items[] = array( 'question' => trim( $parts[0] ), 'answer' => trim( $parts[1] ) );
			}
		}
		return $items;
	}
}
if ( ! function_exists( 'ca_recipe_display_minutes' ) ) {
	function ca_recipe_display_minutes( $minutes ) {
		$minutes = absint( $minutes );
		if ( $minutes < 60 ) {
			return sprintf( _n( '%s min', '%s mins', $minutes, 'crisp-and-air' ), number_format_i18n( $minutes ) );
		}
		$hours = (int) floor( $minutes / 60 );
		$rest  = $minutes % 60;
		return sprintf( _n( '%s hr', '%s hrs', $hours, 'crisp-and-air' ), number_format_i18n( $hours ) ) . ( $rest ? ' ' . sprintf( _n( '%s min', '%s mins', $rest, 'crisp-and-air' ), number_format_i18n( $rest ) ) : '' );
	}
}
if ( ! function_exists( 'ca_recipe_author_photo' ) ) {
	function ca_recipe_author_photo( $user_id, $size = 'thumbnail' ) {
		$image_id = absint( get_user_meta( (int) $user_id, 'ca_author_image_id', true ) );
		return $image_id ? wp_get_attachment_image( $image_id, $size, false, array( 'class' => 'author-photo', 'alt' => '' ) ) : '';
	}
}
if ( ! function_exists( 'ca_recipe_author_bio' ) ) {
	function ca_recipe_author_bio( $user_id ) {
		$bio = get_user_meta( (int) $user_id, 'ca_author_short_bio', true );
		return $bio ? wp_strip_all_tags( $bio ) : get_the_author_meta( 'description', (int) $user_id );
	}
}

add_action( 'after_setup_theme', 'ca_theme_setup' );
function ca_theme_setup() {
	load_theme_textdomain( 'crisp-and-air', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 84, 'width' => 250, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	set_post_thumbnail_size( 900, 675, true );
	add_image_size( 'ca-card', 720, 540, true );
	add_image_size( 'ca-hero', 1440, 1050, true );
	add_image_size( 'ca-author', 180, 180, true );
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'crisp-and-air' ),
			'footer'  => __( 'Footer Navigation', 'crisp-and-air' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'ca_theme_enqueue_assets' );
function ca_theme_enqueue_assets() {
	wp_enqueue_style( 'ca-theme-style', get_stylesheet_uri(), array(), CA_THEME_VERSION );
	wp_enqueue_style( 'ca-theme-main', get_template_directory_uri() . '/assets/css/theme.css', array( 'ca-theme-style' ), CA_THEME_VERSION );
	wp_enqueue_script( 'ca-theme-script', get_template_directory_uri() . '/assets/js/theme.js', array(), CA_THEME_VERSION, true );
}

add_action( 'widgets_init', 'ca_theme_register_widget_areas' );
function ca_theme_register_widget_areas() {
	$areas = array(
		'ca-sidebar'          => __( 'Recipe Sidebar — desktop', 'crisp-and-air' ),
		'ca-after-intro'      => __( 'Recipe: after introduction', 'crisp-and-air' ),
		'ca-before-recipe'    => __( 'Recipe: before recipe card', 'crisp-and-air' ),
		'ca-after-recipe'     => __( 'Recipe: after recipe card', 'crisp-and-air' ),
		'ca-archive-infeed'   => __( 'Recipe archive: in-feed', 'crisp-and-air' ),
		'ca-footer'           => __( 'Footer widgets', 'crisp-and-air' ),
	);
	foreach ( $areas as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}

add_action( 'customize_register', 'ca_theme_customize_register' );
function ca_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'ca_homepage',
		array(
			'title'       => __( 'Crisp & Air Homepage', 'crisp-and-air' ),
			'priority'    => 35,
			'description' => __( 'Edit the homepage introduction and newsletter placement. Featured and popular recipes are selected in each recipe’s Recipe details panel.', 'crisp-and-air' ),
		)
	);
	$controls = array(
		'ca_hero_title' => array(
			'label'       => __( 'Hero headline', 'crisp-and-air' ),
			'default'     => __( 'Air fryer recipes for every day.', 'crisp-and-air' ),
			'sanitize'    => 'sanitize_text_field',
			'control'     => 'text',
		),
		'ca_hero_description' => array(
			'label'       => __( 'Hero description', 'crisp-and-air' ),
			'default'     => __( 'Crisp, satisfying recipes with clear steps, useful timing, and ingredients you can find.', 'crisp-and-air' ),
			'sanitize'    => 'sanitize_textarea_field',
			'control'     => 'textarea',
		),
		'ca_newsletter_title' => array(
			'label'       => __( 'Newsletter heading', 'crisp-and-air' ),
			'default'     => __( 'A little more crisp in your inbox.', 'crisp-and-air' ),
			'sanitize'    => 'sanitize_text_field',
			'control'     => 'text',
		),
		'ca_newsletter_description' => array(
			'label'       => __( 'Newsletter description', 'crisp-and-air' ),
			'default'     => __( 'Fresh air-fryer inspiration and practical cooking notes, whenever you are ready.', 'crisp-and-air' ),
			'sanitize'    => 'sanitize_textarea_field',
			'control'     => 'textarea',
		),
		'ca_newsletter_shortcode' => array(
			'label'       => __( 'Newsletter form shortcode', 'crisp-and-air' ),
			'default'     => '',
			'sanitize'    => 'wp_kses_post',
			'control'     => 'textarea',
		),
		'ca_social_instagram' => array(
			'label'       => __( 'Instagram URL', 'crisp-and-air' ),
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'control'     => 'url',
		),
		'ca_social_pinterest' => array(
			'label'       => __( 'Pinterest URL', 'crisp-and-air' ),
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'control'     => 'url',
		),
	);
	foreach ( $controls as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data['default'],
				'sanitize_callback' => $data['sanitize'],
				'transport'         => 'refresh',
			)
		);
		if ( 'textarea' === $data['control'] ) {
			$wp_customize->add_control( $id, array( 'section' => 'ca_homepage', 'label' => $data['label'], 'type' => 'textarea' ) );
		} else {
			$wp_customize->add_control( $id, array( 'section' => 'ca_homepage', 'label' => $data['label'], 'type' => $data['control'] ) );
		}
	}
	$wp_customize->add_setting(
		'ca_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'ca_hero_image', array( 'section' => 'ca_homepage', 'label' => __( 'Homepage hero image', 'crisp-and-air' ), 'mime_type' => 'image' ) ) );
}

add_action( 'after_switch_theme', 'ca_theme_seed_pages' );
function ca_theme_seed_pages() {
	$pages = array(
		'home' => array( 'Home', '' ),
		'recipes' => array( 'Recipes', '' ),
		'categories' => array( 'Categories', '' ),
		'popular-recipes' => array( 'Popular Recipes', '' ),
		'about' => array( 'About Crisp & Air', '<!-- wp:paragraph --><p>Crisp & Air is a practical home-cooking resource focused on recipes made in air fryers. Our aim is straightforward: clear ingredient lists, useful timing guidance, and steps that explain what to look for. We do not claim culinary, medical, or nutrition credentials. Use your appliance instructions and verify safe doneness with a food thermometer when appropriate.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>We welcome thoughtful recipe questions, corrections, and feedback. Use the Contact page to reach the site editor.</p><!-- /wp:paragraph -->' ),
		'contact' => array( 'Contact', '<!-- wp:paragraph --><p>Send a recipe question, correction, or note to the Crisp & Air editor. Messages go to the site email set in WordPress under Settings → General and are used to respond to your message.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[ca_contact_form]<!-- /wp:shortcode -->' ),
		'privacy-policy' => array( 'Privacy Policy', '<!-- wp:paragraph --><p><strong>Site-owner review:</strong> This editable starter describes the theme’s default features. Update it to match your hosting, contact inbox, analytics, advertising, email-list tools, and applicable requirements before relying on it as your final notice.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Information you send us</h2><!-- /wp:heading --><!-- wp:paragraph --><p>If you use the contact form, your name, email address, and message are sent to the site’s configured WordPress email address so the editor can reply. The form does not create a public profile or store a message in the recipe database. A salted hash of the request address may be held for up to an hour to limit abuse. Your email provider and web host may retain message or security logs.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Cookies and third-party services</h2><!-- /wp:heading --><!-- wp:paragraph --><p>The Crisp & Air theme does not add an advertising or analytics tracker by itself. WordPress, the hosting provider, embedded content, analytics, advertising, and newsletter providers may use cookies or process technical data when enabled. Identify each provider, purpose, retention period, and choices here before activating those services.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Your choices and contact</h2><!-- /wp:heading --><!-- wp:paragraph --><p>For questions or a request about information you submitted, use the Contact page. Add the site owner’s identity, applicable rights, retention practices, and any required privacy-request process before launch.</p><!-- /wp:paragraph -->' ),
		'terms-conditions' => array( 'Terms & Conditions', '<!-- wp:paragraph --><p><strong>Site-owner review:</strong> This general starter is not legal advice. Adapt it to your actual site, jurisdiction, content, and business before treating it as final.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Using this site</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Recipe information is provided for general home-cooking use. Appliances, ingredient sizes, and results vary. Follow your appliance manufacturer’s instructions and use appropriate food-safety practices. Do not rely on this site for medical or dietary advice.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Content and external services</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Before launch, state who owns the site content, how permission to reuse it works, how reader-submitted material is handled, and which third-party services or advertising terms apply.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Contact</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Questions about these terms can be sent through the Contact page.</p><!-- /wp:paragraph -->' ),
		'cookie-policy' => array( 'Cookie Policy', '<!-- wp:paragraph --><p><strong>Site-owner review:</strong> This is a starting point, not a complete cookie inventory. Review it whenever you add hosting, analytics, advertising, video, social, or newsletter integrations.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>The theme itself does not install advertising or analytics cookies. WordPress, the host, and any optional third-party tools may use cookies or similar technologies for essential functions, measurement, embedded media, advertising, or subscriptions. Add a current list of the cookies/providers used on this site, their purposes, durations, and the visitor controls available in your jurisdiction.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>For questions, contact the site editor through the Contact page.</p><!-- /wp:paragraph -->' ),
		'disclaimer' => array( 'Disclaimer', '<!-- wp:paragraph --><p><strong>Site-owner review:</strong> Keep this notice aligned with the recipe content and services you actually publish.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Cooking and food safety</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Air-fryer results vary by appliance, food thickness, starting temperature, and basket loading. Recipe times are guides, not a substitute for checking doneness. Follow current food-safety guidance, your appliance manual, and use a food thermometer when appropriate.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Nutrition and health</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Any nutrition figures, when present, are estimates for the stated serving and can vary with ingredients and brands. This site is for general cooking information, not medical, nutrition, or dietary advice. Consult a qualified professional for personal health needs.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Editorial and advertising</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Disclose any sponsorship, affiliate relationship, or paid placement clearly and near the relevant content. Do not present an advertisement as an independent recommendation.</p><!-- /wp:paragraph -->' ),
	);
	$ids = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			$ids[ $slug ] = (int) $existing->ID;
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page[0],
				'post_name'    => $slug,
				'post_content' => $page[1],
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( ! is_wp_error( $page_id ) ) {
			$ids[ $slug ] = (int) $page_id;
		}
	}
	if ( in_array( trim( (string) get_option( 'blogname' ) ), array( '', 'My Blog', 'My WordPress Website' ), true ) ) {
		update_option( 'blogname', 'Crisp & Air' );
	}
	if ( '' === trim( (string) get_option( 'blogdescription' ) ) ) {
		update_option( 'blogdescription', __( 'The air fryer table', 'crisp-and-air' ) );
	}
	if ( empty( get_option( 'page_on_front' ) ) && ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}
	if ( empty( get_option( 'page_for_posts' ) ) && ! empty( $ids['recipes'] ) ) {
		update_option( 'page_for_posts', $ids['recipes'] );
	}
	if ( empty( get_option( 'wp_page_for_privacy_policy' ) ) && ! empty( $ids['privacy-policy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $ids['privacy-policy'] );
	}
	ca_theme_draft_default_sample_post();
	ca_theme_draft_default_sample_page();
}

/** Keep WordPress's untouched welcome post out of a recipe publication without deleting its content. */
function ca_theme_draft_default_sample_post() {
	$sample_posts = get_posts(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'name'                   => 'hello-world',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( empty( $sample_posts ) ) {
		return;
	}
	$sample_post = $sample_posts[0];
	$default_copy = 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!';
	if ( 'Hello world!' === $sample_post->post_title && $default_copy === trim( wp_strip_all_tags( $sample_post->post_content ) ) ) {
		wp_update_post(
			array(
				'ID'          => (int) $sample_post->ID,
				'post_status' => 'draft',
			)
		);
	}
}

/** Keep the untouched English WordPress example page unpublished without altering owner-edited content. */
function ca_theme_draft_default_sample_page() {
	$sample_pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'name'                   => 'sample-page',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( empty( $sample_pages ) ) {
		return;
	}
	$sample_page = $sample_pages[0];
	if ( 'Sample Page' !== $sample_page->post_title ) {
		return;
	}
	$actual_copy = html_entity_decode( wp_strip_all_tags( $sample_page->post_content ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$actual_copy = trim( preg_replace( '/\s+/', ' ', $actual_copy ) );
	$default_copy = "This is an example page. It's different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this: Hi there! I'm a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin' caught in the rain.) ...or something like this: The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community. As a new WordPress user, you should go to your dashboard to delete this page and create new pages for your content. Have fun!";
	if ( $default_copy !== $actual_copy ) {
		return;
	}
	wp_update_post(
		array(
			'ID'          => (int) $sample_page->ID,
			'post_status' => 'draft',
		)
	);
}

function ca_theme_search_form( $class = '' ) {
	$class     = $class ? ' ' . sanitize_html_class( $class ) : '';
	$search_id = 'ca-search-' . wp_unique_id();
	?>
	<form role="search" method="get" class="ca-search-form<?php echo esc_attr( $class ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Search air fryer recipes', 'crisp-and-air' ); ?></label>
		<input id="<?php echo esc_attr( $search_id ); ?>" type="search" name="s" placeholder="<?php esc_attr_e( 'Search recipes or ingredients…', 'crisp-and-air' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
		<button type="submit" aria-label="<?php esc_attr_e( 'Search recipes', 'crisp-and-air' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.7"></circle><path d="m16 16 4.3 4.3"></path></svg></button>
	</form>
	<?php
}

function ca_theme_ad_slot( $sidebar_id, $label = '' ) {
	if ( ! is_active_sidebar( $sidebar_id ) ) {
		return;
	}
	?>
	<div class="ad-slot" aria-label="<?php echo esc_attr( $label ? $label : __( 'Advertisement', 'crisp-and-air' ) ); ?>">
		<span class="ad-slot__label"><?php esc_html_e( 'Advertisement', 'crisp-and-air' ); ?></span>
		<?php dynamic_sidebar( $sidebar_id ); ?>
	</div>
	<?php
}

function ca_theme_temperature_label( $temperature ) {
	$temperature = trim( sanitize_text_field( (string) $temperature ) );
	if ( '' === $temperature ) {
		return '';
	}
	$temperature = preg_replace( '/\s*(?:°\s*)?F(?:ahrenheit)?\s*$/iu', '', $temperature );
	return trim( $temperature ) . '°F';
}

function ca_theme_image( $image_id, $size = 'ca-card', $alt = '' ) {
	if ( $image_id ) {
		$attachment_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = $attachment_alt;
		}
		if ( '' === $alt ) {
			$alt = get_the_title();
		}
		return wp_get_attachment_image(
			$image_id,
			$size,
			false,
			array(
				'alt'      => $alt,
				'class'    => 'ca-image',
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	}
	return '';
}

function ca_theme_menu_fallback() {
	$links = array(
		__( 'Recipes', 'crisp-and-air' )         => home_url( '/recipes/' ),
		__( 'Categories', 'crisp-and-air' )      => home_url( '/categories/' ),
		__( 'Popular Recipes', 'crisp-and-air' ) => home_url( '/popular-recipes/' ),
		__( 'About', 'crisp-and-air' )           => home_url( '/about/' ),
		__( 'Contact', 'crisp-and-air' )         => home_url( '/contact/' ),
	);
	echo '<ul class="primary-nav__list">';
	foreach ( $links as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function ca_theme_footer_menu_fallback() {
	$links = array(
		__( 'About', 'crisp-and-air' )       => home_url( '/about/' ),
		__( 'Contact', 'crisp-and-air' )     => home_url( '/contact/' ),
		__( 'Terms', 'crisp-and-air' )       => home_url( '/terms-conditions/' ),
		__( 'Cookies', 'crisp-and-air' )     => home_url( '/cookie-policy/' ),
		__( 'Disclaimer', 'crisp-and-air' )  => home_url( '/disclaimer/' ),
	);
	$privacy_page_id = absint( get_option( 'wp_page_for_privacy_policy' ) );
	$privacy_page    = $privacy_page_id ? get_post( $privacy_page_id ) : get_page_by_path( 'privacy-policy', OBJECT, 'page' );
	if ( $privacy_page && 'page' === get_post_type( $privacy_page ) && 'publish' === get_post_status( $privacy_page ) ) {
		$links[ __( 'Privacy Policy', 'crisp-and-air' ) ] = get_permalink( $privacy_page );
	}
	echo '<ul class="footer-nav__list">';
	foreach ( $links as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function ca_theme_category_icon( $term_slug ) {
	$icons = array(
		'chicken'        => '✳',
		'beef'           => '◒',
		'pork'           => '◌',
		'seafood'        => '≈',
		'vegetables'     => '❋',
		'potatoes'       => '✦',
		'breakfast'      => '☼',
		'lunch'          => '◍',
		'dinner'         => '✺',
		'appetizers'     => '◇',
		'snacks'         => '✧',
		'desserts'       => '❀',
		'frozen-foods'   => '❄',
		'healthy'        => '⌁',
		'quick-and-easy' => '↗',
		'vegetarian'     => '❖',
	);
	return isset( $icons[ $term_slug ] ) ? $icons[ $term_slug ] : '✦';
}

function ca_theme_category_terms( $limit = 16 ) {
	$root = get_term_by( 'slug', 'air-fryer-recipes', 'category' );
	$args = array(
		'taxonomy'   => 'category',
		'hide_empty' => false,
		'number'     => $limit,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	if ( $root && ! is_wp_error( $root ) ) {
		$args['parent'] = (int) $root->term_id;
	}
	$terms = get_terms( $args );
	return is_wp_error( $terms ) ? array() : $terms;
}

function ca_theme_recipe_card( $post_id = 0, $variant = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return;
	}
	get_template_part( 'template-parts/recipe-card', null, array( 'post_id' => $post_id, 'variant' => $variant ) );
}

function ca_theme_pagination( $query = null ) {
	global $wp_query;
	$query = $query instanceof WP_Query ? $query : $wp_query;
	if ( ! $query || $query->max_num_pages < 2 ) {
		return;
	}
	$links = paginate_links(
		array(
			'total'     => (int) $query->max_num_pages,
			'current'   => max( 1, (int) get_query_var( 'paged' ) ),
			'type'      => 'list',
			'prev_text' => __( '← Previous', 'crisp-and-air' ),
			'next_text' => __( 'Next →', 'crisp-and-air' ),
		)
	);
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Recipe pages', 'crisp-and-air' ) . '">' . wp_kses_post( $links ) . '</nav>';
	}
}

function ca_theme_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	$items = array( array( 'label' => __( 'Home', 'crisp-and-air' ), 'url' => home_url( '/' ) ) );
	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		if ( $categories ) {
			$category = $categories[0];
			if ( $category->parent ) {
				$ancestors = array_reverse( get_ancestors( $category->term_id, 'category' ) );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, 'category' );
					if ( $ancestor && ! is_wp_error( $ancestor ) && 'air-fryer-recipes' !== $ancestor->slug ) {
						$items[] = array( 'label' => $ancestor->name, 'url' => get_category_link( $ancestor ) );
					}
				}
			}
			if ( 'air-fryer-recipes' !== $category->slug ) {
				$items[] = array( 'label' => $category->name, 'url' => get_category_link( $category ) );
			}
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term && $term->parent ) {
			foreach ( array_reverse( get_ancestors( $term->term_id, 'category' ) ) as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, 'category' );
				if ( $ancestor && ! is_wp_error( $ancestor ) && 'air-fryer-recipes' !== $ancestor->slug ) {
					$items[] = array( 'label' => $ancestor->name, 'url' => get_category_link( $ancestor ) );
				}
			}
		}
		$items[] = array( 'label' => single_cat_title( '', false ), 'url' => '' );
	} elseif ( is_page() ) {
		$parents = array_reverse( get_post_ancestors( get_queried_object_id() ) );
		foreach ( $parents as $parent_id ) {
			$items[] = array( 'label' => get_the_title( $parent_id ), 'url' => get_permalink( $parent_id ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_search() ) {
		$items[] = array( 'label' => __( 'Search', 'crisp-and-air' ), 'url' => '' );
	} else {
		$items[] = array( 'label' => wp_get_document_title(), 'url' => '' );
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'crisp-and-air' ); ?>">
		<ol>
			<?php foreach ( $items as $index => $item ) : ?>
				<li><?php if ( $item['url'] && $index < count( $items ) - 1 ) : ?><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a><?php else : ?><span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span><?php endif; ?></li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

function ca_theme_recipe_summary_items( $post_id ) {
	$prep     = absint( ca_recipe_meta( $post_id, 'prep_minutes' ) );
	$cook     = absint( ca_recipe_meta( $post_id, 'cook_minutes' ) );
	$total    = $prep + $cook;
	$servings = ca_recipe_meta( $post_id, 'servings' );
	$calories = absint( ca_recipe_meta( $post_id, 'calories' ) );
	$course   = ca_recipe_meta( $post_id, 'course' );
	$cuisine  = ca_recipe_meta( $post_id, 'cuisine' );
	$difficulty = ca_recipe_meta( $post_id, 'difficulty' );
	$items = array();
	if ( $prep ) {
		$items[] = array( __( 'Prep', 'crisp-and-air' ), ca_recipe_display_minutes( $prep ) );
	}
	if ( $cook ) {
		$items[] = array( __( 'Cook', 'crisp-and-air' ), ca_recipe_display_minutes( $cook ) );
	}
	if ( $total ) {
		$items[] = array( __( 'Total', 'crisp-and-air' ), ca_recipe_display_minutes( $total ) );
	}
	if ( $servings ) {
		$items[] = array( __( 'Yield', 'crisp-and-air' ), $servings );
	}
	if ( $calories ) {
		$items[] = array( __( 'Calories', 'crisp-and-air' ), sprintf( __( '%s per serving', 'crisp-and-air' ), number_format_i18n( $calories ) ) );
	}
	if ( $course ) {
		$items[] = array( __( 'Course', 'crisp-and-air' ), $course );
	}
	if ( $cuisine ) {
		$items[] = array( __( 'Cuisine', 'crisp-and-air' ), $cuisine );
	}
	if ( $difficulty ) {
		$items[] = array( __( 'Difficulty', 'crisp-and-air' ), $difficulty );
	}
	return $items;
}
