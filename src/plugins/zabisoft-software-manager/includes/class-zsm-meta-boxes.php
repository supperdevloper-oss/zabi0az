<?php
/**
 * Meta boxes: the editing experience for software entries.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta box registration and saving.
 */
class ZSM_Meta_Boxes {

	/**
	 * Nonce action.
	 */
	const NONCE = 'zsm_save_software';

	/**
	 * Hook up.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes_software', array( $this, 'register' ) );
		add_action( 'save_post_software', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Register the boxes.
	 *
	 * @return void
	 */
	public function register() {
		$groups = ZSM_Fields::groups();

		add_meta_box( 'zsm-details', $groups['details']['title'], array( $this, 'render_group' ), 'software', 'normal', 'high', array( 'group' => 'details' ) );
		add_meta_box( 'zsm-requirements', $groups['requirements']['title'], array( $this, 'render_group' ), 'software', 'normal', 'high', array( 'group' => 'requirements' ) );
		add_meta_box( 'zsm-downloads', __( 'Download mirrors', 'zabisoft-software-manager' ), array( $this, 'render_mirrors' ), 'software', 'normal', 'high' );
		add_meta_box( 'zsm-screenshots', __( 'Screenshots', 'zabisoft-software-manager' ), array( $this, 'render_screenshots' ), 'software', 'side', 'default' );
		add_meta_box( 'zsm-content', $groups['content']['title'], array( $this, 'render_group' ), 'software', 'normal', 'default', array( 'group' => 'content' ) );
		add_meta_box( 'zsm-extra', $groups['extra']['title'], array( $this, 'render_group' ), 'software', 'side', 'default', array( 'group' => 'extra' ) );
		add_meta_box( 'zsm-help', __( 'How to use this page', 'zabisoft-software-manager' ), array( $this, 'render_help' ), 'software', 'side', 'low' );
	}

	/**
	 * Render a group of simple fields.
	 *
	 * @param WP_Post $post Post object.
	 * @param array   $box  Box args.
	 * @return void
	 */
	public function render_group( $post, $box ) {
		$group_key = isset( $box['args']['group'] ) ? $box['args']['group'] : 'details';
		$groups    = ZSM_Fields::groups();

		if ( ! isset( $groups[ $group_key ] ) ) {
			return;
		}

		$group = $groups[ $group_key ];

		wp_nonce_field( self::NONCE, 'zsm_nonce' );

		if ( ! empty( $group['description'] ) ) {
			echo '<p class="zsm-help">' . esc_html( $group['description'] ) . '</p>';
		}

		echo '<div class="zsm-grid">';

		foreach ( $group['fields'] as $key => $field ) {
			$value = get_post_meta( $post->ID, $key, true );
			$id    = 'zsm-' . ltrim( $key, '_' );
			$type  = $field['type'];

			echo '<div class="zsm-field zsm-field--' . esc_attr( $type ) . '">';
			printf( '<label for="%1$s"><strong>%2$s</strong></label>', esc_attr( $id ), esc_html( $field['label'] ) );

			$common = array(
				'id'          => $id,
				'name'        => 'zsm_fields[' . $key . ']',
				'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			);

			if ( 'textarea' === $type ) {
				printf(
					'<textarea id="%1$s" name="%2$s" rows="%3$d" placeholder="%4$s">%5$s</textarea>',
					esc_attr( $id ),
					esc_attr( $common['name'] ),
					(int) ( $field['rows'] ?? 5 ),
					esc_attr( $common['placeholder'] ),
					esc_textarea( (string) $value )
				);
			} elseif ( 'number' === $type ) {
				printf(
					'<input type="number" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="1">',
					esc_attr( $id ),
					esc_attr( $common['name'] ),
					esc_attr( (string) $value ),
					esc_attr( (string) ( $field['min'] ?? 0 ) ),
					esc_attr( (string) ( $field['max'] ?? 3600 ) )
				);
			} elseif ( 'date' === $type ) {
				printf(
					'<input type="date" id="%1$s" name="%2$s" value="%3$s">',
					esc_attr( $id ),
					esc_attr( $common['name'] ),
					esc_attr( (string) $value )
				);
			} else {
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s">',
					esc_attr( 'url' === $type ? 'url' : 'text' ),
					esc_attr( $id ),
					esc_attr( $common['name'] ),
					esc_attr( (string) $value ),
					esc_attr( $common['placeholder'] )
				);
			}

			if ( ! empty( $field['description'] ) ) {
				echo '<p class="zsm-help">' . esc_html( $field['description'] ) . '</p>';
			}

			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Render the mirror repeater.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function render_mirrors( $post ) {
		wp_nonce_field( self::NONCE, 'zsm_nonce' );

		$mirrors = get_post_meta( $post->ID, '_zs_mirrors', true );

		if ( is_string( $mirrors ) ) {
			$decoded = json_decode( $mirrors, true );
			$mirrors = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $mirrors ) ) {
			$mirrors = array();
		}
		?>
		<p class="zsm-help">
			<?php esc_html_e( 'Add one row per download option (official website, mirror, portable build, torrent…). The theme prints the destination host next to every button and can verify each URL automatically, once per hour.', 'zabisoft-software-manager' ); ?>
		</p>

		<div id="zsm-mirrors" class="zsm-mirrors" data-next-index="<?php echo esc_attr( count( $mirrors ) ); ?>">
			<?php
			if ( ! $mirrors ) {
				$mirrors = array(
					array(
						'label' => __( 'Official website', 'zabisoft-software-manager' ),
						'url'   => '',
					),
				);
			}

			foreach ( $mirrors as $index => $mirror ) {
				$this->mirror_row( (int) $index, $mirror );
			}
			?>
		</div>

		<p>
			<button type="button" class="button button-secondary" id="zsm-add-mirror">
				<?php esc_html_e( '+ Add mirror', 'zabisoft-software-manager' ); ?>
			</button>
		</p>

		<p class="zsm-help">
			<?php esc_html_e( 'Reminder: only link to installers you are allowed to share, and always prefer the official publisher page.', 'zabisoft-software-manager' ); ?>
		</p>

		<script type="text/html" id="tmpl-zsm-mirror-row">
			<?php
			$this->mirror_row(
				'{{INDEX}}',
				array(
					'label'    => '',
					'url'      => '',
					'size'     => '',
					'type'     => '',
					'note'     => '',
					'platform' => '',
				)
			);
			?>
		</script>
		<?php
	}

	/**
	 * A single mirror row.
	 *
	 * @param int|string $index  Row index or template placeholder.
	 * @param array      $mirror Mirror data.
	 * @return void
	 */
	private function mirror_row( $index, $mirror ) {
		$mirror = wp_parse_args(
			(array) $mirror,
			array(
				'label'    => '',
				'url'      => '',
				'size'     => '',
				'type'     => '',
				'note'     => '',
				'platform' => '',
			)
		);

		$url     = (string) $mirror['url'];
		$status  = '';
		$checked = false;

		if ( $url && class_exists( 'ZSM_Link_Checker' ) ) {
			$result  = ( new ZSM_Link_Checker() )->cached_status( $url );
			$checked = ! empty( $result['time'] );
			$status  = ! empty( $result['ok'] )
				? '<span class="zsm-status zsm-status--ok">' . esc_html__( 'Reachable', 'zabisoft-software-manager' ) . '</span>'
				: '<span class="zsm-status zsm-status--bad">' . esc_html__( 'Problem detected', 'zabisoft-software-manager' ) . '</span>';
		}
		?>
		<div class="zsm-mirror" data-index="<?php echo esc_attr( $index ); ?>">
			<div class="zsm-mirror__head">
				<span class="zsm-mirror__handle" aria-hidden="true">⋮⋮</span>
				<strong class="zsm-mirror__title">
					<?php
					printf(
						/* translators: %s: mirror number. */
						esc_html__( 'Mirror %s', 'zabisoft-software-manager' ),
						'<span class="zsm-mirror__number">' . esc_html( is_numeric( $index ) ? $index + 1 : '#' ) . '</span>'
					);
					?>
				</strong>
				<?php echo $checked ? wp_kses_post( $status ) : ''; ?>
				<button type="button" class="button-link zsm-mirror__remove"><?php esc_html_e( 'Remove', 'zabisoft-software-manager' ); ?></button>
			</div>

			<div class="zsm-grid">
				<div class="zsm-field">
					<label><?php esc_html_e( 'Label', 'zabisoft-software-manager' ); ?></label>
					<input type="text" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $mirror['label'] ); ?>" placeholder="<?php esc_attr_e( 'Official download', 'zabisoft-software-manager' ); ?>">
				</div>
				<div class="zsm-field zsm-field--wide">
					<label><?php esc_html_e( 'URL (https://…)', 'zabisoft-software-manager' ); ?></label>
					<input type="url" class="zsm-mirror__url" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $mirror['url'] ); ?>" placeholder="https://">
					<p class="zsm-mirror__check">
						<button type="button" class="button button-small zsm-check-link"><?php esc_html_e( 'Check link', 'zabisoft-software-manager' ); ?></button>
						<span class="zsm-check-result" role="status"></span>
					</p>
				</div>
				<div class="zsm-field">
					<label><?php esc_html_e( 'Size', 'zabisoft-software-manager' ); ?></label>
					<input type="text" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][size]" value="<?php echo esc_attr( $mirror['size'] ); ?>" placeholder="350 MB">
				</div>
				<div class="zsm-field">
					<label><?php esc_html_e( 'Type', 'zabisoft-software-manager' ); ?></label>
					<input type="text" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][type]" value="<?php echo esc_attr( $mirror['type'] ); ?>" placeholder="<?php esc_attr_e( 'Installer / portable / ISO', 'zabisoft-software-manager' ); ?>">
				</div>
				<div class="zsm-field">
					<label><?php esc_html_e( 'Platform', 'zabisoft-software-manager' ); ?></label>
					<input type="text" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][platform]" value="<?php echo esc_attr( $mirror['platform'] ); ?>" placeholder="Windows 64-bit">
				</div>
				<div class="zsm-field zsm-field--wide">
					<label><?php esc_html_e( 'Note for visitors', 'zabisoft-software-manager' ); ?></label>
					<input type="text" name="zsm_mirrors[<?php echo esc_attr( $index ); ?>][note]" value="<?php echo esc_attr( $mirror['note'] ); ?>" placeholder="<?php esc_attr_e( 'Optional: licence key page, checksum, instructions…', 'zabisoft-software-manager' ); ?>">
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Screenshot picker.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function render_screenshots( $post ) {
		$shots = get_post_meta( $post->ID, '_zs_screenshots', true );
		$shots = is_array( $shots ) ? array_filter( array_map( 'absint', $shots ) ) : array();
		?>
		<p class="zsm-help"><?php esc_html_e( 'Pick images from the media library. They appear in a gallery with a lightbox on the software page.', 'zabisoft-software-manager' ); ?></p>

		<div id="zsm-screenshots" class="zsm-screenshots">
			<ul class="zsm-screenshots__list">
				<?php foreach ( $shots as $id ) : ?>
					<li data-id="<?php echo esc_attr( $id ); ?>">
						<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
						<button type="button" class="zsm-screenshots__remove" aria-label="<?php esc_attr_e( 'Remove image', 'zabisoft-software-manager' ); ?>">&times;</button>
						<input type="hidden" name="zsm_screenshots[]" value="<?php echo esc_attr( $id ); ?>">
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<p>
			<button type="button" class="button" id="zsm-add-screenshots"><?php esc_html_e( 'Add screenshots', 'zabisoft-software-manager' ); ?></button>
		</p>
		<?php
	}

	/**
	 * Help box.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function render_help( $post ) {
		?>
		<ol class="zsm-help-list">
			<li><?php esc_html_e( 'Set the cover image (right column) — it is used in listings and in the page header.', 'zabisoft-software-manager' ); ?></li>
			<li><?php esc_html_e( 'Write the description in the main editor. It becomes the “Overview” section.', 'zabisoft-software-manager' ); ?></li>
			<li><?php esc_html_e( 'Fill in the details, requirements, features and installation steps below.', 'zabisoft-software-manager' ); ?></li>
			<li><?php esc_html_e( 'Add one row per download option and click “Check link” to verify the URL.', 'zabisoft-software-manager' ); ?></li>
			<li><?php esc_html_e( 'Publish, then view the page: sections, table of contents and the countdown gate are generated automatically.', 'zabisoft-software-manager' ); ?></li>
		</ol>
		<?php
	}

	/**
	 * Save everything safely.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['zsm_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['zsm_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Simple fields ------------------------------------------------------.
		if ( isset( $_POST['zsm_fields'] ) && is_array( $_POST['zsm_fields'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per field below.
			$raw = wp_unslash( $_POST['zsm_fields'] );

			foreach ( ZSM_Fields::all_keys() as $key ) {
				if ( ! isset( $raw[ $key ] ) ) {
					continue;
				}

				$field = ZSM_Fields::get( $key );
				$type  = $field ? $field['type'] : 'text';
				$value = $raw[ $key ];

				switch ( $type ) {
					case 'url':
						$clean = esc_url_raw( trim( (string) $value ) );
						break;
					case 'textarea':
						$clean = sanitize_textarea_field( (string) $value );
						break;
					case 'number':
						$clean = ( '' === $value ) ? '' : (string) max( 0, absint( $value ) );
						break;
					case 'date':
						$clean = preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ) ? (string) $value : '';
						break;
					default:
						$clean = sanitize_text_field( (string) $value );
				}

				if ( '' === $clean || null === $clean ) {
					delete_post_meta( $post_id, $key );
				} else {
					update_post_meta( $post_id, $key, $clean );
				}
			}
		}

		// Mirrors ------------------------------------------------------------.
		if ( isset( $_POST['zsm_mirrors'] ) && is_array( $_POST['zsm_mirrors'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised row by row below.
			$rows    = wp_unslash( $_POST['zsm_mirrors'] );
			$mirrors = array();

			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}

				$url = isset( $row['url'] ) ? esc_url_raw( trim( (string) $row['url'] ) ) : '';

				if ( '' === $url || ! wp_http_validate_url( $url ) ) {
					continue;
				}

				$mirrors[] = array(
					'label'    => sanitize_text_field( $row['label'] ?? '' ),
					'url'      => $url,
					'size'     => sanitize_text_field( $row['size'] ?? '' ),
					'type'     => sanitize_text_field( $row['type'] ?? '' ),
					'note'     => sanitize_text_field( $row['note'] ?? '' ),
					'platform' => sanitize_text_field( $row['platform'] ?? '' ),
				);
			}

			if ( $mirrors ) {
				update_post_meta( $post_id, '_zs_mirrors', $mirrors );
			} else {
				delete_post_meta( $post_id, '_zs_mirrors' );
			}

			// Verify the links that changed.
			if ( $mirrors && class_exists( 'ZSM_Link_Checker' ) && get_option( 'zsm_check_on_save', 1 ) ) {
				$checker = new ZSM_Link_Checker();

				foreach ( $mirrors as $mirror ) {
					$checker->check( $mirror['url'], true );
				}
			}
		}

		// Screenshots --------------------------------------------------------.
		$shots = isset( $_POST['zsm_screenshots'] ) ? (array) wp_unslash( $_POST['zsm_screenshots'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cast + absint below.
		$shots = array_values( array_filter( array_map( 'absint', $shots ) ) );

		if ( $shots ) {
			update_post_meta( $post_id, '_zs_screenshots', $shots );
		} else {
			delete_post_meta( $post_id, '_zs_screenshots' );
		}
	}
}
