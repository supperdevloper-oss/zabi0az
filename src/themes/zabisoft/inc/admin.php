<?php
/**
 * Admin experience: dashboard page, notices and quick start.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the theme dashboard page.
 *
 * @return void
 */
function zabisoft_admin_menu() {
	add_menu_page(
		__( 'ZabiSoft theme', 'zabisoft' ),
		__( 'ZabiSoft theme', 'zabisoft' ),
		'edit_theme_options',
		'zabisoft',
		'zabisoft_render_admin_page',
		'dashicons-download',
		59
	);
}
add_action( 'admin_menu', 'zabisoft_admin_menu' );

/**
 * Render the theme dashboard.
 *
 * @return void
 */
function zabisoft_render_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$plugin_active = class_exists( 'ZSM_Plugin' ) || post_type_exists( 'software' );
	$software_count = 0;

	if ( post_type_exists( 'software' ) ) {
		$counts         = wp_count_posts( 'software' );
		$software_count = isset( $counts->publish ) ? (int) $counts->publish : 0;
	}

	$checks = array(
		array(
			'label' => __( 'Theme activated', 'zabisoft' ),
			'done'  => true,
			'text'  => sprintf(
				/* translators: %s: theme name. */
				__( '%s is the active theme.', 'zabisoft' ),
				wp_get_theme()->get( 'Name' )
			),
		),
		array(
			'label' => __( 'Software manager plugin', 'zabisoft' ),
			'done'  => class_exists( 'ZSM_Plugin' ),
			'text'  => class_exists( 'ZSM_Plugin' )
				? __( 'The ZabiSoft Software Manager plugin is active: fields, download mirrors and demo content are available.', 'zabisoft' )
				: __( 'Not detected. The theme still works, but the software fields and the demo importer need the plugin.', 'zabisoft' ),
			'link'  => admin_url( 'plugin-install.php?tab=upload' ),
			'cta'   => __( 'Install plugin', 'zabisoft' ),
		),
		array(
			'label' => __( 'Menus', 'zabisoft' ),
			'done'  => zabisoft_has_menu( 'primary' ),
			'text'  => zabisoft_has_menu( 'primary' )
				? __( 'A primary menu is assigned.', 'zabisoft' )
				: __( 'No primary menu yet — the theme prints a temporary list of your categories.', 'zabisoft' ),
			'link'  => admin_url( 'nav-menus.php' ),
			'cta'   => __( 'Manage menus', 'zabisoft' ),
		),
		array(
			'label' => __( 'Widgets', 'zabisoft' ),
			'done'  => is_active_sidebar( 'sidebar-1' ),
			'text'  => is_active_sidebar( 'sidebar-1' )
				? __( 'The main sidebar has widgets.', 'zabisoft' )
				: __( 'The main sidebar is empty.', 'zabisoft' ),
			'link'  => admin_url( 'widgets.php' ),
			'cta'   => __( 'Manage widgets', 'zabisoft' ),
		),
		array(
			'label' => __( 'Software entries', 'zabisoft' ),
			'done'  => $software_count > 0,
			'text'  => $software_count > 0
				? sprintf(
					/* translators: %d: number of software entries. */
					_n( '%d published software entry.', '%d published software entries.', $software_count, 'zabisoft' ),
					$software_count
				)
				: __( 'No software entries yet. Use “Software → Add new” or import the demo content.', 'zabisoft' ),
			'link'  => admin_url( 'edit.php?post_type=software' ),
			'cta'   => __( 'Manage software', 'zabisoft' ),
		),
	);
	?>
	<div class="wrap zs-admin">
		<h1><?php esc_html_e( 'ZabiSoft — quick start', 'zabisoft' ); ?></h1>
		<p class="zs-admin__intro">
			<?php esc_html_e( 'A short checklist to get the site ready. Every item links to the right WordPress screen.', 'zabisoft' ); ?>
		</p>

		<div class="zs-admin__cards">
			<?php foreach ( $checks as $check ) : ?>
				<div class="zs-admin__card <?php echo $check['done'] ? 'is-done' : 'is-todo'; ?>">
					<h2>
						<span class="zs-admin__dot" aria-hidden="true"></span>
						<?php echo esc_html( $check['label'] ); ?>
					</h2>
					<p><?php echo esc_html( $check['text'] ); ?></p>
					<?php if ( ! empty( $check['link'] ) ) : ?>
						<a class="button" href="<?php echo esc_url( $check['link'] ); ?>">
							<?php echo esc_html( $check['cta'] ?? __( 'Open', 'zabisoft' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<h2><?php esc_html_e( 'Theme options', 'zabisoft' ); ?></h2>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( add_query_arg( 'autofocus[panel]', 'zabisoft_panel', admin_url( 'customize.php' ) ) ); ?>">
				<?php esc_html_e( 'Open the Customizer', 'zabisoft' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Menus', 'zabisoft' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>"><?php esc_html_e( 'Widgets', 'zabisoft' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=zabisoft' ) ); ?>"><?php esc_html_e( 'Refresh this page', 'zabisoft' ); ?></a>
		</p>

		<h2><?php esc_html_e( 'Layout, colours and content', 'zabisoft' ); ?></h2>
		<ul class="zs-admin__list">
			<li><?php esc_html_e( 'Layout & colours: container width, sidebar side, accent colour scheme, header search, top bar.', 'zabisoft' ); ?></li>
			<li><?php esc_html_e( 'Front page: hero banner, popular search links, category strip, which content is listed.', 'zabisoft' ); ?></li>
			<li><?php esc_html_e( 'Software pages: specs table, screenshots, installation steps, download countdown and link checking.', 'zabisoft' ); ?></li>
			<li><?php esc_html_e( 'Footer & social: copyright text, footer columns, social profiles.', 'zabisoft' ); ?></li>
		</ul>

		<p class="description">
			<?php esc_html_e( 'Tip: the theme documentation that came with the download explains every option, in Arabic and English.', 'zabisoft' ); ?>
		</p>
	</div>
	<style>
		.zs-admin__intro { max-width: 70ch; }
		.zs-admin__cards { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); max-width: 1200px; margin: 20px 0 30px; }
		.zs-admin__card { background: #fff; border: 1px solid #dcdcde; border-left-width: 4px; border-radius: 4px; padding: 14px 16px; }
		.zs-admin__card.is-done { border-left-color: #46b450; }
		.zs-admin__card.is-todo { border-left-color: #dba617; }
		.zs-admin__card h2 { font-size: 15px; margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
		.zs-admin__dot { width: 10px; height: 10px; border-radius: 50%; background: #dba617; display: inline-block; }
		.zs-admin__card.is-done .zs-admin__dot { background: #46b450; }
		.zs-admin__list { list-style: disc; padding-left: 20px; }
	</style>
	<?php
}

/**
 * Notice shown to administrators while the companion plugin is missing.
 *
 * @return void
 */
function zabisoft_plugin_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	if ( class_exists( 'ZSM_Plugin' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( $screen && 'toplevel_page_zabisoft' === $screen->id ) {
		return;
	}

	printf(
		'<div class="notice notice-info is-dismissible"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'ZabiSoft theme:', 'zabisoft' ),
		esc_html__( 'install the free “ZabiSoft Software Manager” plugin to manage version numbers, file sizes, system requirements, screenshots and download mirrors for every program.', 'zabisoft' ),
		esc_url( admin_url( 'plugin-install.php?tab=upload' ) ),
		esc_html__( 'Upload plugin', 'zabisoft' )
	);
}
add_action( 'admin_notices', 'zabisoft_plugin_notice' );

/**
 * Welcome message after activation.
 *
 * @return void
 */
function zabisoft_after_switch_theme() {
	set_transient( 'zabisoft_activation_notice', 1, 60 );
}
add_action( 'after_switch_theme', 'zabisoft_after_switch_theme' );

/**
 * Print the activation notice once.
 *
 * @return void
 */
function zabisoft_activation_notice() {
	if ( ! get_transient( 'zabisoft_activation_notice' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	delete_transient( 'zabisoft_activation_notice' );

	printf(
		'<div class="notice notice-success is-dismissible"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Thank you for installing ZabiSoft!', 'zabisoft' ),
		esc_html__( 'Follow the quick start checklist to configure menus, widgets and the software plugin.', 'zabisoft' ),
		esc_url( admin_url( 'admin.php?page=zabisoft' ) ),
		esc_html__( 'Open quick start', 'zabisoft' )
	);
}
add_action( 'admin_notices', 'zabisoft_activation_notice' );
