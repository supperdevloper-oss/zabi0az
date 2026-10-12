<?php
/**
 * Plugin Name:       ZabiSoft Software Manager
 * Plugin URI:        https://github.com/supperdevloper-oss/zabi0az
 * Description:       Adds a real software catalogue to WordPress: a “Software” post type, categories and platforms, version / file size / system requirements / screenshots / installation steps fields, multiple download mirrors with link checking, a settings screen and one click demo content. Designed for the ZabiSoft theme but works with any theme.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            ZabiSoft
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       zabisoft-software-manager
 * Domain Path:       /languages
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZSM_VERSION', '1.0.0' );
define( 'ZSM_FILE', __FILE__ );
define( 'ZSM_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ZSM_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin class.
 */
final class ZSM_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var ZSM_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Settings screen handler.
	 *
	 * @var ZSM_Settings
	 */
	public $settings;

	/**
	 * Retrieve the single instance.
	 *
	 * @return ZSM_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Build the plugin.
	 */
	private function __construct() {
		$this->includes();
		$this->hooks();
	}

	/**
	 * Load files.
	 *
	 * @return void
	 */
	private function includes() {
		require_once ZSM_DIR . 'includes/class-zsm-post-types.php';
		require_once ZSM_DIR . 'includes/class-zsm-fields.php';
		require_once ZSM_DIR . 'includes/class-zsm-meta-boxes.php';
		require_once ZSM_DIR . 'includes/class-zsm-link-checker.php';
		require_once ZSM_DIR . 'includes/class-zsm-admin-columns.php';
		require_once ZSM_DIR . 'includes/class-zsm-settings.php';
		require_once ZSM_DIR . 'includes/class-zsm-demo-content.php';
		require_once ZSM_DIR . 'includes/class-zsm-shortcodes.php';
		require_once ZSM_DIR . 'includes/helpers.php';

		$this->settings = new ZSM_Settings();

		new ZSM_Post_Types();
		new ZSM_Meta_Boxes();
		new ZSM_Admin_Columns();
		new ZSM_Demo_Content();
		new ZSM_Shortcodes();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'wp_ajax_zsm_check_link', array( 'ZSM_Link_Checker', 'ajax_check' ) );

		register_activation_hook( ZSM_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( ZSM_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'zabisoft-software-manager', false, dirname( plugin_basename( ZSM_FILE ) ) . '/languages' );
	}

	/**
	 * Admin CSS and JS.
	 *
	 * @param string $hook Current admin page.
	 * @return void
	 */
	public function admin_assets( $hook ) {
		$screen = get_current_screen();

		$is_plugin_screen = ( $screen && 'software' === $screen->post_type )
			|| false !== strpos( (string) $hook, 'zsm-settings' )
			|| ( $screen && in_array( $screen->id, array( 'edit-software_category', 'edit-software_platform', 'edit-software_tag' ), true ) );

		if ( ! $is_plugin_screen ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'zsm-admin', ZSM_URL . 'assets/css/admin.css', array(), ZSM_VERSION );
		wp_enqueue_script( 'zsm-admin', ZSM_URL . 'assets/js/admin.js', array( 'jquery' ), ZSM_VERSION, true );

		wp_localize_script(
			'zsm-admin',
			'zsmData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'zsm_admin' ),
				'i18n'    => array(
					'checking'      => __( 'Checking…', 'zabisoft-software-manager' ),
					'failed'        => __( 'The check could not be completed. Try again later.', 'zabisoft-software-manager' ),
					'emptyUrl'      => __( 'Enter a URL first.', 'zabisoft-software-manager' ),
					'confirmRemove' => __( 'Remove this download mirror?', 'zabisoft-software-manager' ),
					'pickImages'    => __( 'Choose screenshots', 'zabisoft-software-manager' ),
					'useImages'     => __( 'Use these images', 'zabisoft-software-manager' ),
					'remove'        => __( 'Remove image', 'zabisoft-software-manager' ),
				),
			)
		);
	}

	/**
	 * Activation: register types, seed terms, flush rewrites.
	 *
	 * @return void
	 */
	public function activate() {
		require_once ZSM_DIR . 'includes/class-zsm-post-types.php';
		$types = new ZSM_Post_Types();
		$types->register_post_type();
		$types->register_taxonomies();
		$types->seed_default_terms();

		flush_rewrite_rules();

		update_option( 'zsm_version', ZSM_VERSION );
	}

	/**
	 * Deactivation: only flush rewrite rules.
	 *
	 * @return void
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}
}

ZSM_Plugin::instance();
