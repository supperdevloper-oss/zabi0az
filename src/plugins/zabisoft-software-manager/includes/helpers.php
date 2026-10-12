<?php
/**
 * Small shared helpers.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default options, applied the first time the plugin runs.
 *
 * @return void
 */
function zsm_apply_default_options() {
	$defaults = array(
		'zsm_archive_slug'  => 'softwares',
		'zsm_category_base' => '',
		'zsm_per_page'      => 10,
		'zsm_countdown'     => 10,
		'zsm_check_on_save' => 1,
		'zsm_open_new_tab'  => 1,
	);

	foreach ( $defaults as $key => $value ) {
		if ( false === get_option( $key, false ) ) {
			add_option( $key, $value );
		}
	}
}
add_action( 'admin_init', 'zsm_apply_default_options' );

/**
 * Software archive query: apply the configured per-page value.
 *
 * @param WP_Query $query Query object.
 * @return void
 */
function zsm_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_post_type_archive( 'software' ) && ! $query->is_tax( 'software_category' ) && ! $query->is_tax( 'software_platform' ) ) {
		return;
	}

	$per_page = (int) get_option( 'zsm_per_page', 10 );

	if ( $per_page > 0 ) {
		$query->set( 'posts_per_page', $per_page );
	}
}
add_action( 'pre_get_posts', 'zsm_archive_query' );

/**
 * Hourly/daily cron job that re-checks every mirror URL.
 *
 * @return void
 */
function zsm_cron_check_links() {
	if ( ! get_option( 'zsm_check_cron', 0 ) ) {
		return;
	}

	if ( ! class_exists( 'ZSM_Link_Checker' ) ) {
		return;
	}

	$checker = new ZSM_Link_Checker();
	$posts   = get_posts(
		array(
			'post_type'      => 'software',
			'posts_per_page' => 40,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		)
	);

	foreach ( $posts as $post_id ) {
		$checker->check_post( $post_id, false );
	}
}
add_action( 'zsm_twicedaily_events', 'zsm_cron_check_links' );

/**
 * Schedule / unschedule the cron event depending on the option.
 *
 * @return void
 */
function zsm_sync_cron() {
	$enabled  = (int) get_option( 'zsm_check_cron', 0 );
	$scheduled = wp_next_scheduled( 'zsm_twicedaily_events' );

	if ( $enabled && ! $scheduled ) {
		wp_schedule_event( time() + 600, 'twicedaily', 'zsm_twicedaily_events' );
	} elseif ( ! $enabled && $scheduled ) {
		wp_unschedule_event( $scheduled, 'zsm_twicedaily_events' );
	}
}
add_action( 'update_option_zsm_check_cron', 'zsm_sync_cron' );
