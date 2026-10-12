<?php
/**
 * Settings screen.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin settings page (Software → Settings).
 */
class ZSM_Settings {

	/**
	 * Hook up.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	/**
	 * Add the screen under the Software menu.
	 *
	 * @return void
	 */
	public function menu() {
		add_submenu_page(
			'edit.php?post_type=software',
			__( 'Software settings', 'zabisoft-software-manager' ),
			__( 'Settings', 'zabisoft-software-manager' ),
			'manage_options',
			'zsm-settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Fields and sanitising.
	 *
	 * @return void
	 */
	public function register() {
		$fields = array(
			'zsm_archive_slug'      => array( 'type' => 'text', 'default' => 'softwares' ),
			'zsm_category_base'     => array( 'type' => 'text', 'default' => '' ),
			'zsm_per_page'          => array( 'type' => 'number', 'default' => 10 ),
			'zsm_countdown'         => array( 'type' => 'number', 'default' => 10 ),
			'zsm_check_on_save'     => array( 'type' => 'checkbox', 'default' => 1 ),
			'zsm_check_cron'        => array( 'type' => 'checkbox', 'default' => 0 ),
			'zsm_open_new_tab'      => array( 'type' => 'checkbox', 'default' => 1 ),
			'zsm_disclosure'        => array( 'type' => 'textarea', 'default' => __( 'Download buttons open an external website you did not reach from this page. Always check the publisher and licence before installing anything.', 'zabisoft-software-manager' ) ),
			'zsm_delete_data'       => array( 'type' => 'checkbox', 'default' => 0 ),
		);

		foreach ( $fields as $option => $config ) {
			register_setting(
				'zsm_settings',
				$option,
				array(
					'type'              => 'string',
					'sanitize_callback' => function ( $value ) use ( $config ) {
						switch ( $config['type'] ) {
							case 'number':
								return (string) absint( $value );
							case 'checkbox':
								return $value ? 1 : 0;
							case 'textarea':
								return sanitize_textarea_field( (string) $value );
							default:
								return sanitize_title( (string) $value );
						}
					},
					'default'           => $config['default'],
				)
			);
		}
	}

	/**
	 * Render the screen.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap zsm-settings">
			<h1><?php esc_html_e( 'Software settings', 'zabisoft-software-manager' ); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'zsm_settings' ); ?>

				<h2 class="title"><?php esc_html_e( 'Catalogue', 'zabisoft-software-manager' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="zsm_archive_slug"><?php esc_html_e( 'Archive URL slug', 'zabisoft-software-manager' ); ?></label></th>
						<td>
							<input type="text" id="zsm_archive_slug" name="zsm_archive_slug" value="<?php echo esc_attr( (string) get_option( 'zsm_archive_slug', 'softwares' ) ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Example: “softwares” gives /softwares/ and /softwares/category-name/. Save your permalinks after changing it.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="zsm_category_base"><?php esc_html_e( 'Category URL base', 'zabisoft-software-manager' ); ?></label></th>
						<td>
							<input type="text" id="zsm_category_base" name="zsm_category_base" value="<?php echo esc_attr( (string) get_option( 'zsm_category_base', '' ) ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Leave empty for clean category URLs such as /office-tools/. Enter a word (for example “category”) if a page or a post already uses the same address. Save your permalinks after changing it.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="zsm_per_page"><?php esc_html_e( 'Entries per archive page', 'zabisoft-software-manager' ); ?></label></th>
						<td>
							<input type="number" min="1" max="60" id="zsm_per_page" name="zsm_per_page" value="<?php echo esc_attr( (string) get_option( 'zsm_per_page', 10 ) ); ?>" class="small-text">
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Downloads & link checking', 'zabisoft-software-manager' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="zsm_countdown"><?php esc_html_e( 'Default countdown (seconds)', 'zabisoft-software-manager' ); ?></label></th>
						<td>
							<input type="number" min="0" max="120" id="zsm_countdown" name="zsm_countdown" value="<?php echo esc_attr( (string) get_option( 'zsm_countdown', 10 ) ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Used when an entry does not define its own value. 0 disables the countdown.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Verification', 'zabisoft-software-manager' ); ?></th>
						<td>
							<label><input type="checkbox" name="zsm_check_on_save" value="1" <?php checked( 1, (int) get_option( 'zsm_check_on_save', 1 ) ); ?>> <?php esc_html_e( 'Check every mirror URL when an entry is saved', 'zabisoft-software-manager' ); ?></label><br>
							<label><input type="checkbox" name="zsm_check_cron" value="1" <?php checked( 1, (int) get_option( 'zsm_check_cron', 0 ) ); ?>> <?php esc_html_e( 'Re-check all links every 12 hours automatically (WP-Cron)', 'zabisoft-software-manager' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Visitor experience', 'zabisoft-software-manager' ); ?></th>
						<td>
							<label><input type="checkbox" name="zsm_open_new_tab" value="1" <?php checked( 1, (int) get_option( 'zsm_open_new_tab', 1 ) ); ?>> <?php esc_html_e( 'Open download links in a new tab', 'zabisoft-software-manager' ); ?></label>
							<p class="description"><?php esc_html_e( 'The theme also prints the destination host and adds rel="nofollow noopener" to external links.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="zsm_disclosure"><?php esc_html_e( 'Disclosure text under the download box', 'zabisoft-software-manager' ); ?></label></th>
						<td>
							<textarea id="zsm_disclosure" name="zsm_disclosure" rows="3" class="large-text"><?php echo esc_textarea( (string) get_option( 'zsm_disclosure', '' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Leave empty to use the theme option instead.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Data', 'zabisoft-software-manager' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Uninstall', 'zabisoft-software-manager' ); ?></th>
						<td>
							<label><input type="checkbox" name="zsm_delete_data" value="1" <?php checked( 1, (int) get_option( 'zsm_delete_data', 0 ) ); ?>> <?php esc_html_e( 'Delete all software entries, terms and settings when the plugin is deleted', 'zabisoft-software-manager' ); ?></label>
							<p class="description"><?php esc_html_e( 'Keep this unchecked unless you really want the data gone.', 'zabisoft-software-manager' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Demo content', 'zabisoft-software-manager' ); ?></h2>
			<p><?php esc_html_e( 'Create a few sample entries, pages and menus so you can see the whole system working, or remove them again with one click.', 'zabisoft-software-manager' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'zsm_demo_action', 'zsm_demo_nonce' ); ?>
				<p>
					<button type="submit" name="zsm_demo_action" value="import" class="button button-primary"><?php esc_html_e( 'Import demo content', 'zabisoft-software-manager' ); ?></button>
					<button type="submit" name="zsm_demo_action" value="remove" class="button" onclick="return confirm('<?php echo esc_js( __( 'Delete all demo content created by this plugin?', 'zabisoft-software-manager' ) ); ?>');"><?php esc_html_e( 'Remove demo content', 'zabisoft-software-manager' ); ?></button>
					<?php
					$demo = (int) get_option( 'zsm_demo_imported', 0 );

					if ( $demo ) {
						printf(
							'<span class="description">%s</span>',
							esc_html(
								sprintf(
									/* translators: %s: date. */
									__( 'Demo content was last imported on %s.', 'zabisoft-software-manager' ),
									date_i18n( get_option( 'date_format' ), $demo )
								)
							)
						);
					}
					?>
				</p>
			</form>
		</div>
		<?php
	}
}
