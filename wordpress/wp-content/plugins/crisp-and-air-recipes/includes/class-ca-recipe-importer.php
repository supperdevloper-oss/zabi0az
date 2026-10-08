<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CA_Recipe_Importer {
	const PAGE_SLUG      = 'ca-recipe-import';
	const TRANSIENT_BASE = 'ca_recipe_import_';
	const BATCH_SIZE     = 25;
	const MAX_FILE_BYTES = 20971520; // 20 MB.
	const MAX_ROWS       = 5000;

	private static $columns = array(
		'title', 'category', 'tags', 'excerpt', 'article_content', 'ingredients', 'instructions',
		'prep_minutes', 'cook_minutes', 'air_fryer_temp', 'servings', 'course', 'cuisine',
		'difficulty', 'calories', 'notes', 'tips', 'variations', 'substitutions', 'storage',
		'reheating', 'faqs', 'seo_title', 'meta_description', 'featured_image_file', 'featured', 'popular',
	);

	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'admin_post_ca_recipe_csv_upload', array( __CLASS__, 'handle_upload' ) );
		add_action( 'admin_post_ca_recipe_csv_next', array( __CLASS__, 'handle_next_batch' ) );
	}

	public static function add_page() {
		add_management_page(
			__( 'Recipe Import', 'crisp-and-air-recipes' ),
			__( 'Recipe Import', 'crisp-and-air-recipes' ),
			'edit_posts',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to import recipes.', 'crisp-and-air-recipes' ) );
		}
		$import_id = isset( $_GET['ca_import_id'] ) ? sanitize_key( wp_unslash( $_GET['ca_import_id'] ) ) : '';
		$state     = $import_id ? get_transient( self::TRANSIENT_BASE . $import_id ) : false;
		if ( $state && ( (int) $state['user_id'] !== get_current_user_id() || ! isset( $state['file'] ) ) ) {
			$state = false;
		}
		?>
		<div class="wrap ca-import-wrap">
			<h1><?php esc_html_e( 'Recipe Import', 'crisp-and-air-recipes' ); ?></h1>
			<p><?php esc_html_e( 'Bring in your recipe titles and any reviewed article/card data in small, safe batches. Every imported item is an unpublished draft; nothing is auto-posted and existing titles are skipped.', 'crisp-and-air-recipes' ); ?></p>
			<?php self::render_error_notice(); ?>
			<?php if ( $state ) : ?>
				<?php self::render_progress( $import_id, $state ); ?>
			<?php else : ?>
				<div class="ca-import-panel">
					<h2><?php esc_html_e( '1. Prepare your CSV', 'crisp-and-air-recipes' ); ?></h2>
					<p><?php esc_html_e( 'Use a UTF-8 CSV with a header row and a required title column. The included template shows optional fields. List ingredients and instruction steps on separate lines inside a quoted CSV cell. Leave unknown nutrition or cooking values blank—do not guess.', 'crisp-and-air-recipes' ); ?></p>
					<p><a class="button" href="<?php echo esc_url( CA_RECIPES_URL . 'assets/recipe-import-template.csv' ); ?>" download><?php esc_html_e( 'Download the sample CSV', 'crisp-and-air-recipes' ); ?></a></p>
					<h2><?php esc_html_e( '2. Upload and import drafts', 'crisp-and-air-recipes' ); ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
						<input type="hidden" name="action" value="ca_recipe_csv_upload" />
						<?php wp_nonce_field( 'ca_recipe_csv_upload' ); ?>
						<p><label for="ca-recipe-csv"><strong><?php esc_html_e( 'CSV file', 'crisp-and-air-recipes' ); ?></strong></label></p>
						<p><input type="file" id="ca-recipe-csv" name="recipe_csv" accept=".csv,text/csv" required /></p>
						<p class="description"><?php esc_html_e( 'Maximum 20 MB and 5,000 rows per import. Large imports resume in batches of 25.', 'crisp-and-air-recipes' ); ?></p>
						<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Upload and review row count', 'crisp-and-air-recipes' ); ?></button></p>
					</form>
				</div>
				<div class="ca-import-panel">
					<h2><?php esc_html_e( 'What happens next?', 'crisp-and-air-recipes' ); ?></h2>
					<ul class="ca-publish-checklist">
						<li><?php esc_html_e( 'A title-only row creates an empty draft—not an invented recipe.', 'crisp-and-air-recipes' ); ?></li>
						<li><?php esc_html_e( 'Provided recipe facts, article copy, SEO fields, and matching existing categories are added to the draft.', 'crisp-and-air-recipes' ); ?></li>
						<li><?php esc_html_e( 'Matching recipe titles are skipped, not overwritten. Missing categories fall back to Air Fryer Recipes.', 'crisp-and-air-recipes' ); ?></li>
						<li><?php esc_html_e( 'Photos are never downloaded from URLs. Upload rights-cleared images to the Media Library first, then set featured_image_file to each unique filename.', 'crisp-and-air-recipes' ); ?></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private static function render_error_notice() {
		if ( empty( $_GET['ca_import_error'] ) ) {
			return;
		}
		$error = sanitize_key( wp_unslash( $_GET['ca_import_error'] ) );
		$messages = array(
			'invalid_file'   => __( 'Please choose a readable .csv file under 20 MB.', 'crisp-and-air-recipes' ),
			'invalid_header' => __( 'The CSV must have a unique header row with a title column.', 'crisp-and-air-recipes' ),
			'no_rows'        => __( 'The CSV contains no recipe rows.', 'crisp-and-air-recipes' ),
			'too_many_rows'  => __( 'This import contains more than 5,000 rows. Split it into smaller files.', 'crisp-and-air-recipes' ),
			'import_missing' => __( 'That import session expired or the temporary file is no longer available. Upload the CSV again.', 'crisp-and-air-recipes' ),
		);
		$message = isset( $messages[ $error ] ) ? $messages[ $error ] : __( 'The import could not be started. Check the file and try again.', 'crisp-and-air-recipes' );
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}

	private static function render_progress( $import_id, $state ) {
		$total      = max( 1, (int) $state['total_rows'] );
		$read       = min( (int) $state['read_rows'], $total );
		$percent    = min( 100, (int) floor( ( $read / $total ) * 100 ) );
		$remaining  = max( 0, (int) $state['total_rows'] - (int) $state['read_rows'] );
		?>
		<div class="ca-import-panel">
			<h2><?php echo esc_html( $state['filename'] ); ?></h2>
			<p><?php printf( esc_html__( '%1$d rows found · %2$d drafts created · %3$d skipped · %4$d errors', 'crisp-and-air-recipes' ), (int) $state['total_rows'], (int) $state['imported'], (int) $state['skipped'], (int) $state['errors'] ); ?></p>
			<div class="ca-import-progress" role="progressbar" aria-label="<?php esc_attr_e( 'Import progress', 'crisp-and-air-recipes' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $percent ); ?>"><span style="width: <?php echo esc_attr( $percent ); ?>%"></span></div>
			<p><?php printf( esc_html__( '%1$d%% complete · %2$d rows remaining', 'crisp-and-air-recipes' ), $percent, $remaining ); ?></p>
			<?php if ( ! empty( $state['category_fallbacks'] ) ) : ?>
				<p class="description"><?php printf( esc_html__( '%d rows used the Air Fryer Recipes parent category because the supplied category was not found. You can correct categories in the draft editor.', 'crisp-and-air-recipes' ), (int) $state['category_fallbacks'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $state['image_unmatched'] ) || ! empty( $state['image_ambiguous'] ) ) : ?>
				<p class="description"><?php printf( esc_html__( '%1$d image filenames did not match a Media Library image and %2$d filenames were ambiguous. Review the featured images on those drafts.', 'crisp-and-air-recipes' ), (int) $state['image_unmatched'], (int) $state['image_ambiguous'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $state['complete'] ) ) : ?>
				<div class="notice notice-success inline"><p><?php esc_html_e( 'Import complete. All created recipes remain drafts for editorial review.', 'crisp-and-air-recipes' ); ?></p></div>
				<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_status=draft&post_type=post' ) ); ?>"><?php esc_html_e( 'Review recipe drafts', 'crisp-and-air-recipes' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'tools.php?page=' . self::PAGE_SLUG ) ); ?>"><?php esc_html_e( 'Start another import', 'crisp-and-air-recipes' ); ?></a></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="ca_recipe_csv_next" />
					<input type="hidden" name="import_id" value="<?php echo esc_attr( $import_id ); ?>" />
					<?php wp_nonce_field( 'ca_recipe_import_next_' . $import_id ); ?>
					<p><button type="submit" class="button button-primary"><?php printf( esc_html__( 'Import next %d rows', 'crisp-and-air-recipes' ), self::BATCH_SIZE ); ?></button></p>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function handle_upload() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to import recipes.', 'crisp-and-air-recipes' ) );
		}
		check_admin_referer( 'ca_recipe_csv_upload' );
		$redirect = admin_url( 'tools.php?page=' . self::PAGE_SLUG );
		if ( empty( $_FILES['recipe_csv'] ) || ! isset( $_FILES['recipe_csv']['error'], $_FILES['recipe_csv']['tmp_name'], $_FILES['recipe_csv']['name'], $_FILES['recipe_csv']['size'] ) ) {
			self::redirect_error( 'invalid_file', $redirect );
		}
		$file = $_FILES['recipe_csv'];
		if ( UPLOAD_ERR_OK !== (int) $file['error'] || (int) $file['size'] < 1 || (int) $file['size'] > self::MAX_FILE_BYTES || ! is_uploaded_file( $file['tmp_name'] ) || 'csv' !== strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) ) ) {
			self::redirect_error( 'invalid_file', $redirect );
		}
		$temp_path = wp_tempnam( 'ca-recipe-import.csv' );
		if ( ! $temp_path || ! move_uploaded_file( $file['tmp_name'], $temp_path ) ) {
			if ( $temp_path && file_exists( $temp_path ) ) {
				wp_delete_file( $temp_path );
			}
			self::redirect_error( 'invalid_file', $redirect );
		}
		$scan = self::inspect_csv( $temp_path );
		if ( is_wp_error( $scan ) ) {
			wp_delete_file( $temp_path );
			self::redirect_error( $scan->get_error_code(), $redirect );
		}
		$import_id = strtolower( wp_generate_password( 20, false, false ) );
		$state     = array(
			'user_id'           => get_current_user_id(),
			'file'              => $temp_path,
			'filename'          => sanitize_file_name( $file['name'] ),
			'headers'           => $scan['headers'],
			'offset'            => $scan['offset'],
			'total_rows'        => $scan['total_rows'],
			'read_rows'         => 0,
			'imported'          => 0,
			'skipped'           => 0,
			'errors'             => 0,
			'category_fallbacks' => 0,
			'image_unmatched'    => 0,
			'image_ambiguous'    => 0,
			'complete'          => false,
		);
		set_transient( self::TRANSIENT_BASE . $import_id, $state, 7 * DAY_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'ca_import_id', $import_id, $redirect ) );
		exit;
	}

	private static function inspect_csv( $path ) {
		$handle = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen -- Temporary CSV file created by WordPress for this authenticated import.
		if ( ! $handle ) {
			return new WP_Error( 'invalid_file' );
		}
		$header_row = fgetcsv( $handle, null, ',', '"', '\\' );
		if ( ! is_array( $header_row ) || empty( $header_row ) ) {
			fclose( $handle );
			return new WP_Error( 'invalid_header' );
		}
		$header_row[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $header_row[0] );
		$headers = array();
		foreach ( $header_row as $header ) {
			$header = strtolower( trim( (string) $header ) );
			$header = preg_replace( '/[^a-z0-9_]+/', '_', $header );
			$headers[] = trim( $header, '_' );
		}
		if ( ! in_array( 'title', $headers, true ) || count( $headers ) !== count( array_unique( $headers ) ) ) {
			fclose( $handle );
			return new WP_Error( 'invalid_header' );
		}
		$headers = array_map(
			static function ( $column ) {
				return in_array( $column, self::$columns, true ) ? $column : '';
			},
			$headers
		);
		$total_rows = 0;
		while ( false !== fgetcsv( $handle, null, ',', '"', '\\' ) ) {
			++$total_rows;
			if ( $total_rows > self::MAX_ROWS ) {
				fclose( $handle );
				return new WP_Error( 'too_many_rows' );
			}
		}
		if ( 0 === $total_rows ) {
			fclose( $handle );
			return new WP_Error( 'no_rows' );
		}
		rewind( $handle );
		fgetcsv( $handle, null, ',', '"', '\\' );
		$offset = ftell( $handle );
		fclose( $handle );
		return array(
			'headers'    => $headers,
			'offset'     => $offset,
			'total_rows' => $total_rows,
		);
	}

	public static function handle_next_batch() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to import recipes.', 'crisp-and-air-recipes' ) );
		}
		$import_id = isset( $_POST['import_id'] ) ? sanitize_key( wp_unslash( $_POST['import_id'] ) ) : '';
		if ( '' === $import_id ) {
			self::redirect_error( 'import_missing', admin_url( 'tools.php?page=' . self::PAGE_SLUG ) );
		}
		check_admin_referer( 'ca_recipe_import_next_' . $import_id );
		$transient_key = self::TRANSIENT_BASE . $import_id;
		$state         = get_transient( $transient_key );
		if ( ! is_array( $state ) || (int) $state['user_id'] !== get_current_user_id() || empty( $state['file'] ) || ! is_file( $state['file'] ) ) {
			self::redirect_error( 'import_missing', admin_url( 'tools.php?page=' . self::PAGE_SLUG ) );
		}
		if ( ! empty( $state['complete'] ) ) {
			self::redirect_import( $import_id );
		}
		$handle = fopen( $state['file'], 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen -- Private temporary CSV from the authenticated importer.
		if ( ! $handle || 0 !== fseek( $handle, (int) $state['offset'] ) ) {
			if ( $handle ) {
				fclose( $handle );
			}
			self::redirect_error( 'import_missing', admin_url( 'tools.php?page=' . self::PAGE_SLUG ) );
		}
		$processed = 0;
		while ( $processed < self::BATCH_SIZE ) {
			$row = fgetcsv( $handle, null, ',', '"', '\\' );
			if ( false === $row ) {
				$state['complete'] = true;
				break;
			}
			$state['offset'] = ftell( $handle );
			++$state['read_rows'];
			++$processed;
			$values = array_pad( $row, count( $state['headers'] ), '' );
			$values = array_slice( $values, 0, count( $state['headers'] ) );
			$record = array();
			foreach ( $state['headers'] as $index => $header ) {
				if ( '' !== $header ) {
					$record[ $header ] = isset( $values[ $index ] ) && is_scalar( $values[ $index ] ) ? (string) $values[ $index ] : '';
				}
			}
			$title = isset( $record['title'] ) ? sanitize_text_field( $record['title'] ) : '';
			$title = trim( wp_html_excerpt( $title, 250, '' ) );
			if ( '' === $title || self::title_exists( $title ) ) {
				++$state['skipped'];
				continue;
			}
			$result = self::create_draft( $title, $record );
			if ( is_wp_error( $result ) ) {
				++$state['errors'];
				continue;
			}
			++$state['imported'];
			if ( ! empty( $result['category_fallback'] ) ) {
				++$state['category_fallbacks'];
			}
			if ( 'unmatched' === $result['image_status'] ) {
				++$state['image_unmatched'];
			} elseif ( 'ambiguous' === $result['image_status'] ) {
				++$state['image_ambiguous'];
			}
		}
		fclose( $handle );
		if ( $state['read_rows'] >= $state['total_rows'] ) {
			$state['complete'] = true;
		}
		if ( ! empty( $state['complete'] ) && ! empty( $state['file'] ) && is_file( $state['file'] ) ) {
			wp_delete_file( $state['file'] );
			$state['file'] = '';
		}
		set_transient( $transient_key, $state, 7 * DAY_IN_SECONDS );
		self::redirect_import( $import_id );
	}

	private static function title_exists( $title ) {
		global $wpdb;
		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status IN ('publish','draft','pending','private','future') AND post_title = %s LIMIT 1",
				$title
			)
		);
		return (bool) $existing;
	}

	private static function create_draft( $title, $record ) {
		$content = isset( $record['article_content'] ) ? wp_kses_post( $record['article_content'] ) : '';
		$excerpt = isset( $record['excerpt'] ) ? sanitize_textarea_field( $record['excerpt'] ) : '';
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'draft',
				'post_title'   => $title,
				'post_name'    => sanitize_title( $title ),
				'post_content' => $content,
				'post_excerpt' => $excerpt,
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}
		ca_recipe_store_import_meta( $post_id, $record );
		$category_result = self::assign_category( $post_id, isset( $record['category'] ) ? $record['category'] : '' );
		if ( ! empty( $record['tags'] ) ) {
			$tags = preg_split( '/[,;|]+/', (string) $record['tags'] );
			$tags = array_values( array_filter( array_map( 'sanitize_text_field', array_map( 'trim', $tags ) ) ) );
			wp_set_post_terms( $post_id, array_slice( $tags, 0, 12 ), 'post_tag', false );
		}
		$image_status = self::attach_existing_image( $post_id, isset( $record['featured_image_file'] ) ? $record['featured_image_file'] : '' );
		return array(
			'category_fallback' => $category_result['fallback'],
			'image_status'      => $image_status,
		);
	}

	private static function attach_existing_image( $post_id, $raw_filename ) {
		global $wpdb;
		$raw_filename = trim( (string) $raw_filename );
		if ( '' === $raw_filename || preg_match( '#^https?://#i', $raw_filename ) ) {
			return 'none';
		}
		$filename = sanitize_file_name( wp_basename( $raw_filename ) );
		if ( '' === $filename ) {
			return 'unmatched';
		}
		$image_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID WHERE p.post_type = 'attachment' AND p.post_status IN ('inherit','private') AND p.post_mime_type LIKE 'image/%%' AND pm.meta_key = '_wp_attached_file' AND SUBSTRING_INDEX(pm.meta_value, '/', -1) = %s ORDER BY p.ID DESC LIMIT 2",
				$filename
			)
		);
		if ( 1 === count( $image_ids ) ) {
			set_post_thumbnail( (int) $post_id, (int) $image_ids[0] );
			return 'attached';
		}
		return count( $image_ids ) > 1 ? 'ambiguous' : 'unmatched';
	}

	private static function assign_category( $post_id, $raw_category ) {
		$raw_category = trim( sanitize_text_field( (string) $raw_category ) );
		$category     = null;
		if ( '' !== $raw_category ) {
			$category = get_term_by( 'slug', sanitize_title( $raw_category ), 'category' );
			if ( ! $category ) {
				$category = get_term_by( 'name', $raw_category, 'category' );
			}
		}
		$fallback = false;
		if ( ! $category || is_wp_error( $category ) ) {
			$category = get_term_by( 'slug', 'air-fryer-recipes', 'category' );
			$fallback = '' !== $raw_category;
		}
		if ( $category && ! is_wp_error( $category ) ) {
			wp_set_post_categories( $post_id, array( (int) $category->term_id ), false );
		}
		return array( 'fallback' => $fallback );
	}

	private static function redirect_import( $import_id ) {
		wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE_SLUG, 'ca_import_id' => $import_id ), admin_url( 'tools.php' ) ) );
		exit;
	}

	private static function redirect_error( $error, $redirect ) {
		wp_safe_redirect( add_query_arg( 'ca_import_error', sanitize_key( $error ), $redirect ) );
		exit;
	}
}

CA_Recipe_Importer::boot();
