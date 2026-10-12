<?php
/**
 * Uninstall routine.
 *
 * Runs only when the plugin is deleted from the WordPress admin. Data is kept
 * unless the site owner explicitly enabled “Delete all software entries” in
 * Software → Settings.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$zsm_options = array(
	'zsm_archive_slug',
	'zsm_category_base',
	'zsm_per_page',
	'zsm_countdown',
	'zsm_check_on_save',
	'zsm_check_cron',
	'zsm_open_new_tab',
	'zsm_disclosure',
	'zsm_terms_seeded',
	'zsm_version',
	'zsm_demo_imported',
	'zsm_demo_objects',
	'zsm_demo_sidebars',
	'zsm_demo_widgets_pending',
);

$zsm_delete = (int) get_option( 'zsm_delete_data', 0 );

if ( ! $zsm_delete ) {
	// Keep the content, only clean the scheduled job and link caches.
	wp_clear_scheduled_hook( 'zsm_twicedaily_events' );
	delete_option( 'zsm_delete_data' );

	return;
}

// Remove every software entry and its meta ----------------------------------.
$zsm_posts = get_posts(
	array(
		'post_type'      => 'software',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $zsm_posts as $zsm_post_id ) {
	wp_delete_post( (int) $zsm_post_id, true );
}

// Remove the terms ----------------------------------------------------------.
foreach ( array( 'software_category', 'software_platform', 'software_tag' ) as $zsm_taxonomy ) {
	$zsm_terms = get_terms(
		array(
			'taxonomy'   => $zsm_taxonomy,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( is_wp_error( $zsm_terms ) ) {
		continue;
	}

	foreach ( $zsm_terms as $zsm_term_id ) {
		wp_delete_term( (int) $zsm_term_id, $zsm_taxonomy );
	}
}

// Remove options, transients and scheduled jobs ----------------------------.
foreach ( $zsm_options as $zsm_option ) {
	delete_option( $zsm_option );
}

global $wpdb;

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_zabisoft_link_%' OR option_name LIKE '_transient_timeout_zabisoft_link_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

wp_clear_scheduled_hook( 'zsm_twicedaily_events' );
