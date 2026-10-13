<?php
/**
 * Demo content importer / remover.
 *
 * Creates a small but complete example site (software entries, pages, menus)
 * from the images shipped inside assets/demo, and can remove everything
 * again. Nothing here runs automatically – the site owner starts it from
 * Software → Settings.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Demo content.
 */
class ZSM_Demo_Content {

	/**
	 * Hook up.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_handle' ) );
		add_action( 'admin_init', array( $this, 'maybe_seed_pending_widgets' ), 20 );
	}

	/**
	 * The widgets can only be placed once the theme that registers them is
	 * loaded. If the demo content was imported in the same request that
	 * activated the theme, remember it and do it on the next admin page load.
	 *
	 * @return void
	 */
	public function maybe_seed_pending_widgets() {
		if ( ! get_option( 'zsm_demo_widgets_pending' ) ) {
			return;
		}

		if ( class_exists( 'ZabiSoft_Posts_Widget' ) ) {
			$this->import_widgets();

			if ( get_option( 'zsm_demo_sidebars' ) ) {
				delete_option( 'zsm_demo_widgets_pending' );
			}
		}
	}

	/**
	 * Created attachment IDs, kept so deletion is exact.
	 *
	 * @return array
	 */
	protected function created() {
		$created = get_option( 'zsm_demo_objects', array() );

		return is_array( $created ) ? $created : array();
	}

	/**
	 * Author used for every demo entry.
	 *
	 * @return int
	 */
	protected function author_id() {
		$current = get_current_user_id();

		if ( $current ) {
			return (int) $current;
		}

		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);

		return $admins ? (int) $admins[0] : 1;
	}

	/**
	 * Remember a created object.
	 *
	 * @param string $type Object type.
	 * @param int    $id   Object ID.
	 * @return void
	 */
	protected function remember( $type, $id ) {
		$created = $this->created();

		$created[ $type ][] = (int) $id;

		update_option( 'zsm_demo_objects', $created, false );
	}

	/**
	 * Handle the two buttons on the settings screen.
	 *
	 * @return void
	 */
	public function maybe_handle() {
		if ( ! isset( $_POST['zsm_demo_action'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'zsm_demo_action', 'zsm_demo_nonce' );

		$action = sanitize_key( wp_unslash( $_POST['zsm_demo_action'] ) );

		if ( 'import' === $action ) {
			$this->import();
		} elseif ( 'remove' === $action ) {
			$this->remove();
		}

		wp_safe_redirect( add_query_arg( array( 'post_type' => 'software', 'page' => 'zsm-settings', 'zsm_demo' => $action ), admin_url( 'edit.php' ) ) );
		exit;
	}

	/**
	 * Import the sample site.
	 *
	 * @return void
	 */
	public function import() {
		$terms     = $this->import_terms();
		$images    = $this->import_images();
		$screens   = array_slice( $images['shots'], 0, 3 );
		$pages     = $this->import_pages();
		$last_id   = 0;

		$entries = array(
			array(
				'title'   => 'LibreOffice 2026 Free Download',
				'cat'     => 'office-tools',
				'cover'   => 'office',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version' => '26.2.1',
				'size'    => '365 MB',
				'setup'   => 'LibreOffice-26.2.1-win-x86_64.msi',
				'type'    => 'Offline installer / full standalone setup',
				'compat'  => '32-bit (x86) and 64-bit (x64)',
				'licence' => 'Free and open source (MPL 2.0)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-4 days' ) ),
				'dev'     => 'The Document Foundation',
				'site'    => 'https://www.libreoffice.org/download/download-libreoffice/',
				'langs'   => 'English, Arabic, French, Spanish, 100+ more',
				'req'     => array(
					'os'   => 'Windows 11 / 10 / 8.1, macOS 12 or newer, modern Linux distribution',
					'cpu'  => 'Dual core processor 1.5 GHz or faster',
					'ram'  => '2 GB (4 GB recommended)',
					'disk' => '1.5 GB of free hard disk space',
					'gpu'  => 'OpenGL 2.1 capable graphics adapter (for 3D effects)',
				),
				'features' => "Writer, Calc, Impress, Draw, Base and Math in one download\nOpens and saves Microsoft Office file formats\nPortable and 64-bit builds available\nBuilt-in PDF export and digital signature support\nFully offline – no account and no internet needed",
				'steps'    => "Open the official download page linked below\nPick your operating system and language, then download the installer\nRight click the downloaded file and choose “Run as administrator”\nFollow the setup wizard and pick the components you need\nStart Writer from the Start menu",
				'excerpt'  => 'A complete offline office suite that replaces word processors, spreadsheets and presentation tools. Free, open source and available for Windows, macOS and Linux.',
				'content'  => "<p>A full office suite built by volunteers and sponsored by The Document Foundation. It installs completely locally, works without an internet connection and stores your documents in open formats while still reading Microsoft Office files.</p>\n<p>The package below is the official community build. Use it for letters, reports, budgets and presentations; every module shares the same user interface, so learning one means you already know the others.</p>",
			),
			array(
				'title'   => 'VLC Media Player Free Download',
				'cat'     => 'video-editing',
				'cover'   => 'media',
				'platforms' => array( 'Windows', 'macOS', 'Linux', 'Android', 'iOS' ),
				'version' => '3.0.21',
				'size'    => '43 MB',
				'setup'   => 'vlc-3.0.21-win64.exe',
				'type'    => 'Offline installer / portable ZIP',
				'compat'  => '32-bit (x86) and 64-bit (x64)',
				'licence' => 'Free and open source (GPLv2)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-9 days' ) ),
				'dev'     => 'VideoLAN',
				'site'    => 'https://www.videolan.org/vlc/',
				'langs'   => 'English, Arabic, French, 70+ more',
				'req'     => array(
					'os'   => 'Windows 7 or newer, macOS 10.12+, most Linux distributions',
					'cpu'  => 'Any x86 or Apple Silicon processor',
					'ram'  => '512 MB (2 GB for 4K playback)',
					'disk' => '120 MB of free space',
				),
				'features' => "Plays nearly every container and codec without extra packs\nHardware accelerated decoding for 4K and HEVC\nSubtitle sync, audio delay and video filters\nNetwork streams, screen capture and podcast playback\nExtensions picker inside the player",
				'steps'    => "Download the installer for your operating system\nRun the file and accept the licence\nChoose the components to install and finish the wizard\nOpen any media file and drag it into the player window",
				'excerpt'  => 'An open source player that handles almost every media format without codec packs. Includes installer and portable builds plus optional extensions.',
				'content'  => "<p>The player is maintained by VideoLAN, a non-profit organisation. It decodes media locally, so nothing is uploaded and no account is required.</p>\n<p>Version 3.0.21 fixes subtitle rendering problems and improves HEVC hardware decoding on older graphics cards.</p>",
			),
			array(
				'title'   => 'GIMP Image Editor Free Download',
				'cat'     => '3d-designing',
				'cover'   => 'graphics',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version' => '3.2.4',
				'size'    => '295 MB',
				'setup'   => 'gimp-3.2.4-setup.exe',
				'type'    => 'Offline installer / full standalone setup',
				'compat'  => '64-bit (x64), ARM64 for Windows',
				'licence' => 'Free and open source (GPLv3)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-16 days' ) ),
				'dev'     => 'The GIMP Team',
				'site'    => 'https://www.gimp.org/downloads/',
				'langs'   => 'English, Arabic, French, 40+ more',
				'req'     => array(
					'os'   => 'Windows 10 or newer, macOS 11+, Linux',
					'cpu'  => 'Dual core 2 GHz or faster',
					'ram'  => '4 GB (8 GB for large photos)',
					'disk' => '1.3 GB of free space',
					'gpu'  => 'Not required, but a modern GPU speeds up GEGL filters',
				),
				'features' => "Layers, masks, channels and full non-destructive editing\nScript-Fu and Python plug-ins for batch work\nGraphic tablet pressure support\nColour management for print and screen\nSingle window or classic multi window mode",
				'steps'    => "Download the installer from the official download page\nRun the wizard and keep the default plug-in folder\nStart GIMP and choose the interface language\nOpen Edit → Preferences to tune memory and tablet settings",
				'excerpt'  => 'Professional layer based photo retouching, composition and image conversion, developed by volunteers and released under the GPL.',
				'content'  => "<p>GIMP covers photo retouching, free-form drawing, format conversion and batch processing through plug-ins.</p>\n<p>Build 3.2.4 ships an improved symmetry painting tool, faster GEGL operations and better support for high resolution displays.</p>",
			),
			array(
				'title'   => '7-Zip File Archiver Free Download',
				'cat'     => 'disk-management',
				'cover'   => 'utility',
				'platforms' => array( 'Windows' ),
				'version' => '26.00',
				'size'    => '1.6 MB',
				'setup'   => '7z2600-x64.exe',
				'type'    => 'Offline installer',
				'compat'  => '64-bit (x64) and 32-bit (x86)',
				'licence' => 'Free and open source (LGPL)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-23 days' ) ),
				'dev'     => 'Igor Pavlov',
				'site'    => 'https://www.7-zip.org/download.html',
				'langs'   => 'English, Arabic, French, 80+ more',
				'req'     => array(
					'os'   => 'Windows 7 or newer (a separate build exists for Linux)',
					'cpu'  => 'Any processor, x64 build recommended',
					'ram'  => '256 MB',
					'disk' => '5 MB of free space',
				),
				'features' => "High compression ratio with the 7z format\nReads ZIP, RAR, TAR, GZ, ISO, CAB and more\nAES-256 encryption with optional file name hiding\nCommand line version for scripts and backups\nShell integration in Windows Explorer",
				'steps'    => "Download the installer matching your Windows version\nRun it and choose the installation folder\nOptionally enable the Explorer context menu entries\nRight click any archive and choose 7-Zip → Extract Here",
				'excerpt'  => 'A tiny, very fast archiver with strong compression, AES-256 encryption and support for almost every archive format.',
				'content'  => "<p>7-Zip is free for personal and commercial use and is famous for its compression ratio compared with commercial tools.</p>\n<p>The 26.00 release adds faster Zstandard decompression and improves the Windows 11 context menu integration.</p>",
			),
			array(
				'title'   => 'Blender 3D Creation Suite Free Download',
				'cat'     => '3d-visualization',
				'cover'   => '3d',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version' => '5.1',
				'size'    => '340 MB',
				'setup'   => 'blender-5.1.0-windows-x64.msi',
				'type'    => 'Offline installer / portable ZIP',
				'compat'  => '64-bit (x64), ARM64',
				'licence' => 'Free and open source (GPLv2)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-30 days' ) ),
				'dev'     => 'Blender Foundation',
				'site'    => 'https://www.blender.org/download/',
				'langs'   => 'English, Arabic, 30+ more',
				'req'     => array(
					'os'   => 'Windows 10 or newer, macOS 12+, Linux with glibc 2.28+',
					'cpu'  => 'Quad core processor, AVX2 capable',
					'ram'  => '8 GB (16 GB or more for simulation work)',
					'disk' => '2 GB of free space',
					'gpu'  => 'OpenGL 4.3 / Vulkan capable card with 2 GB video memory',
				),
				'features' => "Modelling, sculpting, UV and retopology tools\nCycles and EEVEE renderers with GPU acceleration\nVideo sequence editor and motion tracking\nRigging, animation and geometry nodes\nPython API and a large add-on ecosystem",
				'steps'    => "Download the installer or the portable archive\nInstall it, then run the compatibility check from the splash screen\nEnable GPU rendering in Edit → Preferences → System\nLoad the included demo scenes from Help → Splash to test performance",
				'excerpt'  => 'A complete 3D pipeline for modelling, animation, rendering and video editing, backed by the Blender Foundation.',
				'content'  => "<p>Blender covers the whole 3D workflow in a single application: model, sculpt, rig, animate, simulate, render and composite.</p>\n<p>Version 5.1 improves GPU light baking and adds new geometry node assets.</p>",
			),
			array(
				'title'   => 'Audacity Audio Editor Free Download',
				'cat'     => 'video-editing',
				'cover'   => 'audio',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version' => '3.7.5',
				'size'    => '32 MB',
				'setup'   => 'audacity-win-3.7.5-64bit.exe',
				'type'    => 'Offline installer / portable ZIP',
				'compat'  => '64-bit (x64)',
				'licence' => 'Free and open source (GPLv2)',
				'date'    => gmdate( 'Y-m-d', strtotime( '-37 days' ) ),
				'dev'     => 'The Audacity Team',
				'site'    => 'https://www.audacityteam.org/download/',
				'langs'   => 'English, Arabic, French, 45+ more',
				'req'     => array(
					'os'   => 'Windows 10 or newer, macOS 11+, Linux',
					'cpu'  => 'Dual core 1.8 GHz',
					'ram'  => '2 GB',
					'disk' => '200 MB of free space',
				),
				'features' => "Record from microphone, loopback or external audio devices\nMulti track editing with unlimited undo\nNoise reduction, equaliser, compressor and limiter effects\nExport to MP3, WAV, FLAC, OGG and M4A\nMacro recorder for repetitive tasks",
				'steps'    => "Download and install the program\nConnect your microphone and pick it in the audio settings\nPress the red record button and capture your audio\nSelect a region and apply effects, then export with File → Export",
				'excerpt'  => 'Record and edit multi track audio, remove background noise and export to every common audio format.',
				'content'  => "<p>Audacity is used for podcast editing, lecture recording, sound repair and quick audio conversion.</p>\n<p>Both the installer and the portable archive are offered; the portable build runs from a USB stick without installation rights.</p>",
			),
			array(
				'title'     => 'Ubuntu Desktop 24.04 LTS Free Download',
				'cat'       => 'operating-systems',
				'cover'     => 'os',
				'platforms' => array( 'Linux' ),
				'version'   => '24.04.3 LTS',
				'size'      => '5.7 GB',
				'setup'     => 'ubuntu-24.04.3-desktop-amd64.iso',
				'type'      => 'ISO image / offline installer',
				'compat'    => '64-bit (x64)',
				'licence'   => 'Free and open source (GPL and others)',
				'date'      => gmdate( 'Y-m-d', strtotime( '-2 days' ) ),
				'dev'       => 'Canonical',
				'site'      => 'https://ubuntu.com/download/desktop',
				'langs'     => 'English, Arabic, French, 60+ more',
				'req'       => array(
					'os'   => 'A 64-bit PC with UEFI or BIOS',
					'cpu'  => 'Dual core 2 GHz or faster',
					'ram'  => '4 GB (8 GB recommended)',
					'disk' => '25 GB of free hard disk space',
					'gpu'  => 'Any adapter with a community or vendor driver',
				),
				'features' => "Long term support release with security updates until 2029\nComplete desktop with browser, office suite and media applications\nSigned ISO image that can be verified before installing\nRuns live from the USB stick beside an existing system\nSoftware centre with tens of thousands of free applications",
				'steps'    => "Download the ISO image and check its SHA-256 checksum\nWrite it to a USB stick with Rufus or Balena Etcher\nBoot the computer from the stick and choose Try or Install\nPick the language, keyboard layout and destination disk\nCreate your account and restart when the installer finishes",
				'excerpt'  => 'A free long term support desktop system with a complete set of applications, regular security updates and full Arabic support.',
				'content'  => "<p>Ubuntu Desktop is built on the same open source foundation as the other programs on this page: the system, the drivers and every application can be inspected, replaced or removed. The installer detects an existing Windows installation and offers either to replace it or to keep both systems side by side.</p>\n<p>Version 24.04 is a long term support release, so it keeps receiving security fixes and hardware support without a subscription.</p>",
			),
			array(
				'title'     => 'ClamAV Free Download',
				'cat'       => 'antivirus',
				'cover'     => 'security',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version'   => '1.4.3',
				'size'      => '148 MB',
				'setup'     => 'clamav-1.4.3.win.x64.msi',
				'type'      => 'Offline installer / portable',
				'compat'    => '32-bit (x86) and 64-bit (x64)',
				'licence'   => 'Free and open source (GPLv2)',
				'date'      => gmdate( 'Y-m-d', strtotime( '-6 days' ) ),
				'dev'       => 'Cisco Talos',
				'site'      => 'https://www.clamav.net/downloads',
				'langs'     => 'English and 20+ more',
				'req'       => array(
					'os'   => 'Windows 10 or newer, macOS 12+, most Linux distributions',
					'cpu'  => '1 GHz processor or faster',
					'ram'  => '1 GB (2 GB recommended)',
					'disk' => '500 MB of free space',
					'gpu'  => 'Not required',
				),
				'features' => "Signature database refreshed several times a day\nOn demand scans of files, folders and removable drives\nScans run locally, so no file is uploaded anywhere\nCommand line scanner plus a graphical front end\nOptional mail gateway and file server components",
				'steps'    => "Download the package for your operating system\nRun the installer and keep the suggested folders\nStart the update so the signature database is fetched\nPick the folders to examine and start the scan\nQuarantine or delete whatever the report lists",
				'excerpt'  => 'A free open source virus scanner with a signature database refreshed several times a day and completely local scanning.',
				'content'  => "<p>ClamAV is the scanner behind many mail servers and file gateways. The desktop build brings the same engine to a normal PC: signatures are downloaded, files are compared against them locally, and nothing is sent to a third party.</p>\n<p>It is a good second opinion tool beside the antivirus that ships with Windows or macOS, and it is the scanner used on Linux where commercial suites are rare.</p>",
			),
			array(
				'title'     => 'TestDisk and PhotoRec Free Download',
				'cat'       => 'data-recovery',
				'cover'     => 'recovery',
				'platforms' => array( 'Windows', 'macOS', 'Linux' ),
				'version'   => '7.2',
				'size'      => '42 MB',
				'setup'     => 'testdisk-7.2.win.zip',
				'type'      => 'Portable archive - no installation',
				'compat'    => '32-bit (x86) and 64-bit (x64)',
				'licence'   => 'Free and open source (GPLv2+)',
				'date'      => gmdate( 'Y-m-d', strtotime( '-13 days' ) ),
				'dev'       => 'CGSecurity',
				'site'      => 'https://www.cgsecurity.org/',
				'langs'     => 'English and 15+ more',
				'req'       => array(
					'os'   => 'Windows 7 or newer, macOS 10.9+, Linux',
					'cpu'  => 'Any processor',
					'ram'  => '256 MB',
					'disk' => 'Free space on another drive for the recovered files',
					'gpu'  => 'Not required',
				),
				'features' => "Recovers deleted files from memory cards, USB sticks and hard disks\nRebuilds lost partitions and repairs boot sectors\nPhotoRec matches file signatures, so it works without a file system\nOpens the disk read only: nothing is written to the drive being examined\nRuns from a bootable disk when the system no longer starts",
				'steps'    => "Unpack the archive - the program does not need to be installed\nConnect the media to examine and start TestDisk\nChoose Create to write a log, then select the disk\nPick the partition table type and run the Analyse step\nCopy the files PhotoRec finds to a different drive",
				'excerpt'  => 'Open source recovery tools that rebuild lost partitions and rescue deleted files from memory cards, USB sticks and hard disks.',
				'content'  => "<p>TestDisk repairs partition tables and boot sectors; PhotoRec ignores the file system completely and rebuilds files by matching their signatures, which is what makes it work on cards that a camera or a phone has already declared empty.</p>\n<p>Both tools are portable and open the drive read only, so the original data is left untouched while the recovered copies are written to another disk.</p>",
			),
			array(
				'title'     => 'uGet Download Manager Free Download',
				'cat'       => 'download-managers',
				'cover'     => 'download',
				'platforms' => array( 'Windows', 'Linux' ),
				'version'   => '2.2.3',
				'size'      => '18 MB',
				'setup'     => 'uget-2.2.3-win64.zip',
				'type'      => 'Offline installer / portable archive',
				'compat'    => '32-bit (x86) and 64-bit (x64)',
				'licence'   => 'Free and open source (LGPLv3)',
				'date'      => gmdate( 'Y-m-d', strtotime( '-17 days' ) ),
				'dev'       => 'uGet project',
				'site'      => 'https://ugetdm.com/',
				'langs'     => 'English, Arabic, French, 30+ more',
				'req'       => array(
					'os'   => 'Windows 7 or newer, most Linux distributions',
					'cpu'  => 'Any x86 processor',
					'ram'  => '512 MB',
					'disk' => '120 MB of free space',
					'gpu'  => 'Not required',
				),
				'features' => "Resumes interrupted downloads and keeps the queue after a restart\nSegmented transfers with several connections per file\nTakes over links and media addresses from the browser\nScheduler, clipboard monitor and batch downloads\nOne profile shared between the Windows and Linux builds",
				'steps'    => "Download the archive for your operating system\nUnpack it and start the program - no installation required\nAdd a download by pasting the address or dropping the link\nSet the number of connections and the target folder\nLet the queue run; paused transfers continue where they stopped",
				'excerpt'  => 'A free download manager that resumes broken transfers, queues links and takes over downloads from your browser.',
				'content'  => "<p>Large files rarely arrive in one piece: connections drop, mirrors slow down or the browser gives up at ninety percent. A download manager keeps the partial file, reconnects and continues from where it stopped.</p>\n<p>uGet is open source and portable, and the same profile can be used on Windows and Linux, so the queue survives a change of machine.</p>",
			),
		);

		foreach ( $entries as $entry ) {
			$post_id = $this->create_software( $entry, $terms, $images, $screens );

			if ( $post_id ) {
				$last_id = $post_id;
			}
		}

		$this->import_posts();
		$this->import_widgets();
		$this->import_menu( $terms, $pages, $last_id );

		update_option( 'zsm_demo_imported', time(), false );
	}

	/**
	 * Create the taxonomy terms.
	 *
	 * @return array
	 */
	protected function import_terms() {
		$categories = array(
			'operating-systems' => __( 'Operating Systems', 'zabisoft-software-manager' ),
			'office-tools'      => __( 'Office Tools', 'zabisoft-software-manager' ),
			'download-managers' => __( 'Download Managers', 'zabisoft-software-manager' ),
			'video-editing'     => __( 'Video Editing', 'zabisoft-software-manager' ),
			'3d-designing'      => __( '3D & Graphic Design', 'zabisoft-software-manager' ),
			'antivirus'         => __( 'Antivirus', 'zabisoft-software-manager' ),
			'data-recovery'     => __( 'Data Recovery', 'zabisoft-software-manager' ),
			'disk-management'   => __( 'Utilities', 'zabisoft-software-manager' ),
		);

		$created = array(
			'categories' => array(),
			'platforms'  => array(),
		);

		foreach ( $categories as $slug => $name ) {
			$term = term_exists( $slug, 'software_category' );

			if ( ! $term ) {
				$term = wp_insert_term( $name, 'software_category', array( 'slug' => $slug ) );
			} else {
				$term_id   = (int) ( is_array( $term ) ? $term['term_id'] : $term );
				$term_data = get_term( $term_id, 'software_category' );

				if ( $term_data && ! is_wp_error( $term_data ) && $term_data->name !== $name ) {
					wp_update_term( $term_id, 'software_category', array( 'name' => $name ) );
				}
			}

			if ( ! is_wp_error( $term ) ) {
				$created['categories'][ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			}
		}

		foreach ( array( 'Windows', 'macOS', 'Linux', 'Android', 'iOS' ) as $platform ) {
			$term = term_exists( $platform, 'software_platform' );

			if ( ! $term ) {
				$term = wp_insert_term( $platform, 'software_platform' );
			}

			if ( ! is_wp_error( $term ) ) {
				$created['platforms'][ strtolower( $platform ) ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			}
		}

		return $created;
	}

	/**
	 * Copy the bundled images into the media library.
	 *
	 * @return array
	 */
	protected function import_images() {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$upload = wp_upload_dir();
		$result = array(
			'covers' => array(),
			'shots'  => array(),
		);

		$files = array(
			'cover-office.jpg'   => 'covers',
			'cover-media.jpg'    => 'covers',
			'cover-graphics.jpg' => 'covers',
			'cover-utility.jpg'  => 'covers',
			'cover-3d.jpg'       => 'covers',
			'cover-audio.jpg'    => 'covers',
			'cover-os.jpg'       => 'covers',
			'cover-security.jpg' => 'covers',
			'cover-recovery.jpg' => 'covers',
			'cover-download.jpg' => 'covers',
			'shot-1.jpg'         => 'shots',
			'shot-2.jpg'         => 'shots',
			'shot-3.jpg'         => 'shots',
		);

		foreach ( $files as $file => $bucket ) {
			$source = ZSM_DIR . 'assets/demo/' . $file;

			if ( ! file_exists( $source ) ) {
				continue;
			}

			$destination = trailingslashit( $upload['path'] ) . $file;

			if ( ! file_exists( $destination ) && ! @copy( $source, $destination ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			$title = ucwords( str_replace( array( 'cover-', 'shot-', '.jpg' ), array( '', __( 'Screenshot', 'zabisoft-software-manager' ) . '-', '' ), $file ) );
			$id    = wp_insert_attachment(
				array(
					'post_mime_type' => 'image/jpeg',
					'post_title'     => $title,
					'post_content'   => '',
					'post_status'    => 'inherit',
				),
				$destination
			);

			if ( is_wp_error( $id ) || ! $id ) {
				continue;
			}

			$metadata = wp_generate_attachment_metadata( $id, $destination );

			if ( ! is_wp_error( $metadata ) && $metadata ) {
				wp_update_attachment_metadata( $id, $metadata );
			}

			update_post_meta( $id, '_zsm_demo', 1 );

			$this->remember( 'attachments', $id );

			$result[ $bucket ][ str_replace( array( 'cover-', 'shot-', '.jpg' ), '', $file ) ] = (int) $id;
		}

		return $result;
	}

	/**
	 * Insert one software entry with all of its meta data.
	 *
	 * @param array $entry    Entry data.
	 * @param array $terms    Terms created earlier.
	 * @param array $images   Image IDs created earlier.
	 * @param array $screens  Screenshot IDs.
	 * @return int
	 */
	protected function create_software( $entry, $terms, $images, $screens ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'software',
				'post_status'  => 'publish',
				'post_title'   => $entry['title'],
				'post_name'    => sanitize_title( str_replace( ' Free Download', '', $entry['title'] ) ),
				'post_excerpt' => $entry['excerpt'],
				'post_content' => $entry['content'],
				'post_author'  => $this->author_id(),
				'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . wp_rand( 1, 14 ) . ' days' ) ),
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		$this->remember( 'posts', $post_id );

		if ( ! empty( $terms['categories'][ $entry['cat'] ] ) ) {
			wp_set_object_terms( $post_id, array( (int) $terms['categories'][ $entry['cat'] ] ), 'software_category' );
		}

		$platforms = array();

		foreach ( (array) $entry['platforms'] as $platform ) {
			if ( ! empty( $terms['platforms'][ strtolower( $platform ) ] ) ) {
				$platforms[] = (int) $terms['platforms'][ strtolower( $platform ) ];
			}
		}

		if ( $platforms ) {
			wp_set_object_terms( $post_id, $platforms, 'software_platform' );
		}

		$meta = array(
			'_zs_version'       => $entry['version'],
			'_zs_file_size'     => $entry['size'],
			'_zs_setup_file'    => $entry['setup'],
			'_zs_setup_type'    => $entry['type'],
			'_zs_compatibility' => $entry['compat'],
			'_zs_license'       => $entry['licence'],
			'_zs_release_date'  => $entry['date'],
			'_zs_developer'     => $entry['dev'],
			'_zs_website'       => $entry['site'],
			'_zs_languages'     => $entry['langs'],
			'_zs_req_os'        => $entry['req']['os'] ?? '',
			'_zs_req_cpu'       => $entry['req']['cpu'] ?? '',
			'_zs_req_ram'       => $entry['req']['ram'] ?? '',
			'_zs_req_disk'      => $entry['req']['disk'] ?? '',
			'_zs_req_gpu'       => $entry['req']['gpu'] ?? '',
			'_zs_features'      => $entry['features'],
			'_zs_install_steps' => $entry['steps'],
			'_zs_countdown'     => 10,
		);

		foreach ( $meta as $key => $value ) {
			if ( '' !== $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		update_post_meta(
			$post_id,
			'_zs_mirrors',
			array(
				array(
					'label'    => __( 'Official website (recommended)', 'zabisoft-software-manager' ),
					'url'      => $entry['site'],
					'size'     => $entry['size'],
					'type'     => __( 'Official release', 'zabisoft-software-manager' ),
					'platform' => implode( ' / ', (array) $entry['platforms'] ),
					'note'     => __( 'Publisher page – always the safest place to download from.', 'zabisoft-software-manager' ),
				),
				array(
					'label'    => __( 'Release notes', 'zabisoft-software-manager' ),
					'url'      => $entry['site'],
					'type'     => __( 'Documentation', 'zabisoft-software-manager' ),
					'platform' => implode( ' / ', (array) $entry['platforms'] ),
					'note'     => __( 'Version history and checksums published by the developer.', 'zabisoft-software-manager' ),
				),
			)
		);

		// Cover image -------------------------------------------------------.
		$cover_key = $entry['cover'];

		if ( ! empty( $images['covers'][ $cover_key ] ) ) {
			set_post_thumbnail( $post_id, (int) $images['covers'][ $cover_key ] );
		}

		if ( $screens ) {
			update_post_meta( $post_id, '_zs_screenshots', array_map( 'intval', (array) $screens ) );
		}

		return (int) $post_id;
	}

	/**
	 * Two normal blog posts, so the “blog” side of the theme is visible too.
	 *
	 * @return void
	 */
	protected function import_posts() {
		$posts = array(
			array(
				'title'   => 'How to check that a download link is safe',
				'excerpt' => 'Five quick checks before you run any installer: source, signature, size, licence and file name.',
				'body'    => "<p>Every download button on this site prints the destination host, and the software pages keep a checked list of mirrors.</p>\n<h2>1. Read the host name</h2>\n<p>Official publisher domains end with the vendor name. If a page sends you to an unrelated host, close it.</p>\n<h2>2. Compare the size</h2>\n<p>An installer that should be around 300 MB but weighs 3 MB is not the file you asked for.</p>\n<h2>3. Verify the signature</h2>\n<p>Windows shows the publisher of a signed installer when you open the file properties.</p>",
			),
			array(
				'title'   => 'Portable or installed: which build should you choose?',
				'excerpt' => 'Portable builds keep everything in one folder — useful on locked-down computers, but not always up to date.',
				'body'    => "<p>Many entries in this catalogue offer both an installer and a portable archive. Here is how to pick.</p>\n<h2>Choose the installer when…</h2>\n<ul><li>You want automatic updates and file associations.</li><li>The program installs services or drivers.</li></ul>\n<h2>Choose the portable build when…</h2>\n<ul><li>You cannot install software (school or work computer).</li><li>You want to carry the program on a USB stick.</li></ul>",
			),
		);

		$category = term_exists( 'guides', 'category' );

		if ( ! $category ) {
			$category = wp_insert_term( __( 'Guides', 'zabisoft-software-manager' ), 'category', array( 'slug' => 'guides' ) );
		}

		$category_id = 0;

		if ( ! is_wp_error( $category ) ) {
			$category_id = (int) ( is_array( $category ) ? $category['term_id'] : $category );

			if ( ! term_exists( 'tutorials', 'category' ) ) {
				wp_insert_term( __( 'Tutorials', 'zabisoft-software-manager' ), 'category', array( 'slug' => 'tutorials' ) );
			}
		}

		// WordPress ships a “Hello world!” post with every installation. Keep it,
		// but move it behind the downloads instead of letting it lead the front
		// page, and give it one of the demo categories.
		$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );

		if ( $hello ) {
			wp_update_post(
				array(
					'ID'        => $hello->ID,
					'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( '-34 days' ) ),
				)
			);

			$tutorials = term_exists( 'tutorials', 'category' );

			if ( $tutorials ) {
				wp_set_post_terms( $hello->ID, array( (int) ( is_array( $tutorials ) ? $tutorials['term_id'] : $tutorials ) ), 'category' );
			}
		}

		foreach ( $posts as $item ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => $item['title'],
					'post_excerpt' => $item['excerpt'],
					'post_content' => $item['body'],
					'post_author'  => $this->author_id(),
					// Older than the software entries, so the front page opens with
					// downloads and the guides sit behind the pagination.
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . wp_rand( 18, 30 ) . ' days' ) ),
				),
				true
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			if ( $category_id ) {
				wp_set_object_terms( $post_id, array( $category_id ), 'category' );
			}

			$this->remember( 'posts', $post_id );
		}
	}

	/**
	 * The standard pages the site needs.
	 *
	 * @return array Slugs mapped to IDs.
	 */
	protected function import_pages() {
		$pages = array(
			'about'          => array(
				'title'    => __( 'About', 'zabisoft-software-manager' ),
				'template' => 'page-templates/template-about.php',
				'body'     => "<p>ZabiSoft is a small catalogue of free and open source programs, maintained by hand. Every entry documents the version, the file size, the supported operating systems and the real licence before a download button is printed.</p>\n<p>We only link to official publisher pages and to mirrors we are allowed to share, and each link is verified automatically once per day.</p>",
			),
			'contact-us'     => array(
				'title'    => __( 'Contact us', 'zabisoft-software-manager' ),
				'template' => 'page-templates/template-contact.php',
				'body'     => "<p>Questions, corrections and requests are welcome. Use the form, and mention the exact program and version so we can reproduce the issue.</p>",
			),
			'privacy-policy' => array(
				'title'    => __( 'Privacy policy', 'zabisoft-software-manager' ),
				'template' => '',
				'body'     => "<p>This page explains which data the website stores and why.</p>\n<h2>What we collect</h2>\n<p>Contact form submissions (name, email, message) so we can answer you, and anonymous visit statistics through the hosting provider.</p>\n<h2>Cookies</h2>\n<p>WordPress sets a cookie when you leave a comment. The countdown on download pages is generated in the browser and stores nothing.</p>\n<h2>Your rights</h2>\n<p>Ask us to delete your message at any time by writing to the contact address.</p>",
			),
			'disclaimer'     => array(
				'title'    => __( 'Disclaimer', 'zabisoft-software-manager' ),
				'template' => '',
				'body'     => "<p>The content of this website is provided for information purposes only.</p>\n<p>All trademarks, program names and logos belong to their respective owners and are used descriptively. We do not host or distribute installers: download buttons point to the official website of each publisher, and the destination host is printed next to every button.</p>\n<p>Always verify that you have the right to install and use a program in your country or organisation.</p>",
			),
			'dmca'           => array(
				'title'    => __( 'DMCA', 'zabisoft-software-manager' ),
				'template' => '',
				'body'     => "<p>If you are a rights holder and believe a page on this site links to material you own, write to the contact address with the URL and a description of the work. Verified requests are handled within two working days.</p>",
			),
			'sitemap'        => array(
				'title'    => __( 'Sitemap', 'zabisoft-software-manager' ),
				'template' => 'page-templates/template-sitemap.php',
				'body'     => '',
			),
		);

		$created = array();

		foreach ( $pages as $slug => $page ) {
			$existing = get_page_by_path( $slug, OBJECT, 'page' );

			if ( $existing ) {
				// WordPress creates a draft “Privacy Policy” placeholder page on
				// install – publish it instead of leaving a broken menu link.
				if ( 'publish' !== $existing->post_status ) {
					wp_update_post(
						array(
							'ID'           => $existing->ID,
							'post_status'  => 'publish',
							'post_content' => $existing->post_content ? $existing->post_content : $page['body'],
						)
					);

					update_post_meta( $existing->ID, '_zsm_demo', 1 );
					$this->remember( 'posts', $existing->ID );
				}

				if ( $page['template'] && ! get_post_meta( $existing->ID, '_wp_page_template', true ) ) {
					update_post_meta( $existing->ID, '_wp_page_template', $page['template'] );
				}

				$created[ $slug ] = (int) $existing->ID;
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_content' => $page['body'],
					'post_author'  => $this->author_id(),
				),
				true
			);

			if ( is_wp_error( $page_id ) || ! $page_id ) {
				continue;
			}

			if ( $page['template'] ) {
				update_post_meta( $page_id, '_wp_page_template', $page['template'] );
			}

			update_post_meta( $page_id, '_zsm_demo', 1 );

			$this->remember( 'posts', $page_id );

			$created[ $slug ] = (int) $page_id;
		}

		return $created;
	}

	/**
	 * Some installations still carry a legacy copy of the widget arrangement in
	 * the theme mods. That copy wins over the option on the front end, so it is
	 * removed whenever this plugin writes the arrangement itself.
	 *
	 * @return void
	 */
	protected function clear_legacy_widget_mod() {
		$key  = 'theme_mods_' . get_stylesheet();
		$mods = get_option( $key );

		if ( ! is_array( $mods ) || ! isset( $mods['sidebars_widgets'] ) ) {
			return;
		}

		unset( $mods['sidebars_widgets'] );

		update_option( $key, $mods );
	}

	/**
	 * Fill the main sidebar with the theme widgets.
	 *
	 * Widgets the owner already arranged are never overwritten: the previous
	 * arrangement is stored and put back when the demo content is removed.
	 *
	 * @return void
	 */
	protected function import_widgets() {
		if ( ! in_array( get_stylesheet(), array( 'zabisoft', 'zabisoft-zip' ), true ) ) {
			return;
		}

		if ( ! class_exists( 'ZabiSoft_Posts_Widget' ) ) {
			// The theme was activated in this very request, so its widgets are
			// not registered yet – try again on the next admin page load.
			update_option( 'zsm_demo_widgets_pending', 1, false );

			return;
		}

		$sidebars = (array) get_option( 'sidebars_widgets', array() );
		$current  = (array) ( $sidebars['sidebar-1'] ?? array() );

		foreach ( $current as $widget_id ) {
			if ( 0 === strpos( (string) $widget_id, 'zabisoft_' ) ) {
				return;
			}
		}

		update_option( 'zsm_demo_sidebars', $sidebars, false );

		$posts    = (array) get_option( 'widget_zabisoft_posts', array() );
		$terms    = (array) get_option( 'widget_zabisoft_terms', array() );
		$search   = (array) get_option( 'widget_zabisoft_search', array() );

		$posts[1] = array(
			'title'     => __( 'Latest software', 'zabisoft-software-manager' ),
			'number'    => 5,
			'order'     => 'recent',
			'post_type' => 'software',
		);

		$terms[1] = array(
			'title'      => __( 'Software categories', 'zabisoft-software-manager' ),
			'taxonomy'   => 'software_category',
			'show_count' => true,
		);

		$search[1] = array(
			'title' => __( 'Search Your Favourite Software & Download For PC', 'zabisoft-software-manager' ),
			'text'  => __( 'Type a program name — for example “office” or “player” — then press Enter.', 'zabisoft-software-manager' ),
		);

		// WordPress stores multi-widgets with a `_multiwidget` marker; without it
		// the instance is wrapped twice and the widget renders nothing.
		$posts['_multiwidget']  = 1;
		$terms['_multiwidget']  = 1;
		$search['_multiwidget'] = 1;

		update_option( 'widget_zabisoft_posts', $posts );
		update_option( 'widget_zabisoft_terms', $terms );
		update_option( 'widget_zabisoft_search', $search );

		$inactive = (array) ( $sidebars['wp_inactive_widgets'] ?? array() );

		$sidebars['wp_inactive_widgets'] = array_values( array_unique( array_merge( $inactive, $current ) ) );
		$sidebars['sidebar-1']           = array( 'zabisoft_search-1', 'zabisoft_posts-1', 'zabisoft_terms-1' );

		if ( function_exists( 'wp_set_sidebars_widgets' ) ) {
			wp_set_sidebars_widgets( $sidebars );
		} else {
			update_option( 'sidebars_widgets', $sidebars );
		}

		$this->clear_legacy_widget_mod();
	}

	/**
	 * Build the menus and assign them to the theme locations.
	 *
	 * @param array $terms Terms.
	 * @param array $pages Page IDs.
	 * @param int   $last_id A recent software entry, used for the “Getintopc” slot.
	 * @return void
	 */
	protected function import_menu( $terms, $pages, $last_id ) {
		if ( ! function_exists( 'wp_create_nav_menu' ) ) {
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
		}

		$locations  = get_registered_nav_menus();
		$main_menu  = wp_get_nav_menu_object( 'Main menu' );
		$menu_id    = $main_menu ? (int) $main_menu->term_id : 0;

		if ( ! $menu_id ) {
			$menu_id = wp_create_nav_menu( 'Main menu' );

			if ( is_wp_error( $menu_id ) ) {
				return;
			}

			$this->remember( 'menus', $menu_id );
		}

		// Start from an empty menu: re-running the importer after a theme or
		// plugin update must not leave stale items behind.
		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $zs_stale ) {
			wp_delete_post( $zs_stale->ID, true );
		}

		$order = 0;
		$items = array(
			array(
				'title' => __( 'Home', 'zabisoft-software-manager' ),
				// A relative URL keeps the link valid whatever domain the site
				// is served from (preview host, staging, live).
				'url'   => '/',
			),
		);

		// The reference site lists its categories in this order.
		$menu_slugs = array(
			'operating-systems',
			'download-managers',
			'office-tools',
			'video-editing',
			'antivirus',
			'data-recovery',
		);

		foreach ( $menu_slugs as $slug ) {
			if ( empty( $terms['categories'][ $slug ] ) ) {
				continue;
			}

			$items[] = array( 'term' => array( 'software_category', $terms['categories'][ $slug ] ) );
		}

		if ( $last_id ) {
			$items[] = array( 'object' => array( 'software', $last_id ) );
		}

		if ( ! empty( $pages['contact-us'] ) ) {
			// A real page item, so the link follows the page if its slug changes.
			$items[] = array( 'page' => (int) $pages['contact-us'] );
		}

		foreach ( $items as $item ) {
			$order++;

			if ( isset( $item['term'] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-type'      => 'taxonomy',
						'menu-item-object'    => $item['term'][0],
						'menu-item-object-id' => $item['term'][1],
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order,
					)
				);
				continue;
			}

			if ( isset( $item['object'] ) ) {
				$object = get_post( $item['object'][1] );

				if ( ! $object ) {
					continue;
				}

				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'software',
						'menu-item-object-id' => $object->ID,
						'menu-item-title'     => __( 'Getintopc com', 'zabisoft-software-manager' ),
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order,
					)
				);
				continue;
			}

			if ( isset( $item['page'] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'page',
						'menu-item-object-id' => (int) $item['page'],
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order,
					)
				);
				continue;
			}

			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-type'     => 'custom',
					'menu-item-title'    => $item['title'],
					'menu-item-url'      => $item['url'],
					'menu-item-status'   => 'publish',
					'menu-item-position' => $order,
				)
			);
		}

		// Footer menu -------------------------------------------------------.
		$footer      = wp_get_nav_menu_object( 'Footer menu' );
		$footer_id   = $footer ? (int) $footer->term_id : 0;

		if ( ! $footer_id ) {
			$footer_id = wp_create_nav_menu( 'Footer menu' );

			if ( ! is_wp_error( $footer_id ) ) {
				$this->remember( 'menus', $footer_id );
			}
		}

		if ( $footer_id && ! is_wp_error( $footer_id ) ) {
			// Built from scratch, like the main menu.
			foreach ( (array) wp_get_nav_menu_items( $footer_id ) as $zs_stale ) {
				wp_delete_post( $zs_stale->ID, true );
			}

			$order = 0;

			// Same links as the reference site's footer: About, Contact Us,
			// Privacy Policy, DMCA, Disclaimer, Sitemap.
			foreach ( array( 'about', 'contact-us', 'privacy-policy', 'dmca', 'disclaimer', 'sitemap' ) as $slug ) {
				if ( empty( $pages[ $slug ] ) ) {
					continue;
				}

				$order++;

				wp_update_nav_menu_item(
					$footer_id,
					0,
					array(
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'page',
						'menu-item-object-id' => (int) $pages[ $slug ],
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order,
					)
				);
			}
		}

		// Assign the locations the active theme offers ----------------------.
		$assignments = get_theme_mod( 'nav_menu_locations', array() );

		if ( isset( $locations['primary'] ) || array_key_exists( 'primary', (array) $locations ) ) {
			$assignments['primary'] = $menu_id;
		}

		if ( array_key_exists( 'footer', (array) $locations ) && $footer_id && ! is_wp_error( $footer_id ) ) {
			$assignments['footer'] = $footer_id;
		}

		if ( array_key_exists( 'topbar', (array) $locations ) && $footer_id && ! is_wp_error( $footer_id ) ) {
			// The top bar shows the secondary links (About, Contact, …), never a
			// second copy of the main menu.
			$assignments['topbar'] = $footer_id;
		}

		if ( $assignments ) {
			set_theme_mod( 'nav_menu_locations', $assignments );
		}
	}

	/**
	 * Delete everything this importer created.
	 *
	 * @return void
	 */
	public function remove() {
		$created = $this->created();

		foreach ( array( 'posts', 'attachments' ) as $type ) {
			if ( empty( $created[ $type ] ) ) {
				continue;
			}

			foreach ( (array) $created[ $type ] as $id ) {
				wp_delete_post( (int) $id, true );
			}
		}

		if ( ! empty( $created['menus'] ) ) {
			foreach ( (array) $created['menus'] as $menu_id ) {
				wp_delete_nav_menu( (int) $menu_id );
			}

			set_theme_mod( 'nav_menu_locations', array() );
		}

		$sidebars = get_option( 'zsm_demo_sidebars' );

		if ( is_array( $sidebars ) ) {
			if ( function_exists( 'wp_set_sidebars_widgets' ) ) {
				wp_set_sidebars_widgets( $sidebars );
			} else {
				update_option( 'sidebars_widgets', $sidebars );
			}

			$this->clear_legacy_widget_mod();

			delete_option( 'zsm_demo_sidebars' );
		}

		delete_option( 'zsm_demo_objects' );
		delete_option( 'zsm_demo_imported' );
		delete_option( 'zsm_demo_widgets_pending' );
	}
}
