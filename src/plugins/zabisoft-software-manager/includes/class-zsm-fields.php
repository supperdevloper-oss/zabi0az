<?php
/**
 * Field definitions for software entries.
 *
 * This single source of truth is used by the meta boxes, the REST API
 * registration, the demo importer and the theme templates.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field schema.
 */
class ZSM_Fields {

	/**
	 * Every field, grouped.
	 *
	 * @return array
	 */
	public static function groups() {
		return array(
			'details'      => array(
				'title'       => __( 'Program details', 'zabisoft-software-manager' ),
				'description' => __( 'Shown in the “Technical setup details” table of the theme.', 'zabisoft-software-manager' ),
				'fields'      => array(
					'_zs_version'       => array(
						'label'       => __( 'Version', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => '2026.1',
						'description' => __( 'The version of the program you are describing.', 'zabisoft-software-manager' ),
					),
					'_zs_file_size'     => array(
						'label'       => __( 'File size', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => '350 MB',
					),
					'_zs_setup_file'    => array(
						'label'       => __( 'Setup file name', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => 'program-setup-2026.exe',
					),
					'_zs_setup_type'    => array(
						'label'       => __( 'Setup type', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => __( 'Offline installer / full standalone setup', 'zabisoft-software-manager' ),
					),
					'_zs_compatibility' => array(
						'label'       => __( 'Compatibility', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => '64-bit (x64)',
					),
					'_zs_license'       => array(
						'label'       => __( 'Licence', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => __( 'Free / open source, trial, commercial…', 'zabisoft-software-manager' ),
						'description' => __( 'Always state the real licence so visitors know what to expect.', 'zabisoft-software-manager' ),
					),
					'_zs_release_date'  => array(
						'label' => __( 'Last update', 'zabisoft-software-manager' ),
						'type'  => 'date',
					),
					'_zs_developer'     => array(
						'label'       => __( 'Developer / publisher', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => 'The Document Foundation',
					),
					'_zs_website'       => array(
						'label' => __( 'Official website', 'zabisoft-software-manager' ),
						'type'  => 'url',
					),
					'_zs_languages'     => array(
						'label'       => __( 'Interface languages', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => 'English, Arabic, French…',
					),
				),
			),
			'requirements' => array(
				'title'       => __( 'System requirements', 'zabisoft-software-manager' ),
				'description' => __( 'Only the fields you fill in are printed on the website.', 'zabisoft-software-manager' ),
				'fields'      => array(
					'_zs_req_os'    => array(
						'label'       => __( 'Operating system', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => 'Windows 11 / 10 / 8.1 (64-bit), macOS 12+, Linux',
					),
					'_zs_req_cpu'   => array(
						'label'       => __( 'Processor', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => __( 'Dual core 2 GHz or faster', 'zabisoft-software-manager' ),
					),
					'_zs_req_ram'   => array(
						'label'       => __( 'Memory (RAM)', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => '4 GB',
					),
					'_zs_req_disk'  => array(
						'label'       => __( 'Hard disk space', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => '2 GB of free space',
					),
					'_zs_req_gpu'   => array(
						'label'       => __( 'Graphics card', 'zabisoft-software-manager' ),
						'type'        => 'text',
						'placeholder' => __( 'OpenGL 2.1 capable GPU', 'zabisoft-software-manager' ),
					),
					'_zs_req_other' => array(
						'label' => __( 'Other requirements', 'zabisoft-software-manager' ),
						'type'  => 'textarea',
						'rows'  => 3,
					),
				),
			),
			'content'      => array(
				'title'       => __( 'Features & installation', 'zabisoft-software-manager' ),
				'description' => __( 'One entry per line. They are printed as bullet points and as numbered steps.', 'zabisoft-software-manager' ),
				'fields'      => array(
					'_zs_features'      => array(
						'label'       => __( 'Key features', 'zabisoft-software-manager' ),
						'type'        => 'textarea',
						'rows'        => 7,
						'placeholder' => __( "Runs completely offline\nSupports 40+ languages\nExports to PDF and DOCX", 'zabisoft-software-manager' ),
					),
					'_zs_install_steps' => array(
						'label'       => __( 'Installation instructions', 'zabisoft-software-manager' ),
						'type'        => 'textarea',
						'rows'        => 7,
						'placeholder' => __( "Download the installer from the official website\nRight click the file and choose “Run as administrator”\nFollow the setup wizard\nRestart the computer", 'zabisoft-software-manager' ),
					),
				),
			),
			'extra'        => array(
				'title'  => __( 'Page options', 'zabisoft-software-manager' ),
				'fields' => array(
					'_zs_countdown' => array(
						'label'       => __( 'Countdown before the download button appears (seconds)', 'zabisoft-software-manager' ),
						'type'        => 'number',
						'description' => __( 'Leave empty to use the value from the plugin settings. Use 0 to disable the countdown for this entry.', 'zabisoft-software-manager' ),
						'min'         => 0,
						'max'         => 120,
					),
				),
			),
		);
	}

	/**
	 * Flat list of meta keys.
	 *
	 * @return array
	 */
	public static function all_keys() {
		$keys = array();

		foreach ( self::groups() as $group ) {
			$keys = array_merge( $keys, array_keys( $group['fields'] ) );
		}

		$keys[] = '_zs_screenshots';
		$keys[] = '_zs_mirrors';

		return $keys;
	}

	/**
	 * Field configuration lookup.
	 *
	 * @param string $key Meta key.
	 * @return array|null
	 */
	public static function get( $key ) {
		foreach ( self::groups() as $group ) {
			if ( isset( $group['fields'][ $key ] ) ) {
				return $group['fields'][ $key ];
			}
		}

		return null;
	}
}
