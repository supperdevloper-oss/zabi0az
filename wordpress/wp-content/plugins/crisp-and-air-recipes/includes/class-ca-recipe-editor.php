<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes_post', 'ca_recipe_register_meta_boxes' );
function ca_recipe_register_meta_boxes() {
	add_meta_box( 'ca-recipe-details', __( 'Recipe details', 'crisp-and-air-recipes' ), 'ca_recipe_render_details_box', 'post', 'normal', 'high' );
	add_meta_box( 'ca-recipe-seo', __( 'SEO & sharing', 'crisp-and-air-recipes' ), 'ca_recipe_render_seo_box', 'post', 'side', 'default' );
	add_meta_box( 'ca-recipe-publishing-checklist', __( 'Before you publish', 'crisp-and-air-recipes' ), 'ca_recipe_render_checklist_box', 'post', 'side', 'high' );
}

function ca_recipe_render_field( $key, $label, $type = 'text', $help = '', $attrs = array() ) {
	$value       = ca_recipe_meta( get_the_ID(), $key );
	$field_id    = 'ca-field-' . esc_attr( $key );
	$common_attrs = '';
	foreach ( $attrs as $attr => $attr_value ) {
		if ( 'textarea' === $type && 'rows' === $attr ) {
			continue;
		}
		$common_attrs .= ' ' . esc_attr( $attr ) . '="' . esc_attr( $attr_value ) . '"';
	}
	?>
	<div class="ca-admin-field ca-admin-field-<?php echo esc_attr( $type ); ?>">
		<label for="<?php echo esc_attr( $field_id ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea id="<?php echo esc_attr( $field_id ); ?>" name="ca_recipe[<?php echo esc_attr( $key ); ?>]" rows="<?php echo isset( $attrs['rows'] ) ? absint( $attrs['rows'] ) : 5; ?>"<?php echo $common_attrs; ?>><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input id="<?php echo esc_attr( $field_id ); ?>" type="<?php echo 'number' === $type ? 'number' : 'text'; ?>" name="ca_recipe[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>"<?php echo $common_attrs; ?> />
		<?php endif; ?>
		<?php if ( $help ) : ?><p class="description"><?php echo esc_html( $help ); ?></p><?php endif; ?>
	</div>
	<?php
}

function ca_recipe_render_details_box( $post ) {
	wp_nonce_field( 'ca_save_recipe_details', 'ca_recipe_details_nonce' );
	?>
	<div class="ca-recipe-admin-intro">
		<p><strong><?php esc_html_e( 'Keep the card factual and useful.', 'crisp-and-air-recipes' ); ?></strong> <?php esc_html_e( 'Enter one ingredient per line and one instruction step per paragraph. Add nutrition only when it is reasonably supported. These fields create the printable card and Recipe structured data.', 'crisp-and-air-recipes' ); ?></p>
	</div>
	<div class="ca-admin-grid ca-admin-grid-facts">
		<?php
		ca_recipe_render_field( 'prep_minutes', __( 'Prep time (minutes)', 'crisp-and-air-recipes' ), 'number', '', array( 'min' => 0, 'max' => 1440, 'step' => 1 ) );
		ca_recipe_render_field( 'cook_minutes', __( 'Cook time (minutes)', 'crisp-and-air-recipes' ), 'number', '', array( 'min' => 0, 'max' => 1440, 'step' => 1 ) );
		ca_recipe_render_field( 'servings', __( 'Servings / yield', 'crisp-and-air-recipes' ), 'text', __( 'Example: 4 servings', 'crisp-and-air-recipes' ) );
		ca_recipe_render_field( 'air_fryer_temp', __( 'Air fryer temperature (°F)', 'crisp-and-air-recipes' ), 'text', __( 'Example: 390. Include a range only when it is intentional.', 'crisp-and-air-recipes' ) );
		ca_recipe_render_field( 'course', __( 'Course', 'crisp-and-air-recipes' ), 'text', __( 'Example: Dinner, Side Dish, Dessert', 'crisp-and-air-recipes' ) );
		ca_recipe_render_field( 'cuisine', __( 'Cuisine', 'crisp-and-air-recipes' ), 'text', __( 'Optional. Leave blank if not applicable.', 'crisp-and-air-recipes' ) );
		ca_recipe_render_field( 'difficulty', __( 'Difficulty', 'crisp-and-air-recipes' ), 'text', __( 'Optional: Easy, Moderate, or Advanced', 'crisp-and-air-recipes' ) );
		ca_recipe_render_field( 'calories', __( 'Calories per serving', 'crisp-and-air-recipes' ), 'number', __( 'Optional; do not guess. Use a reliable calculation.', 'crisp-and-air-recipes' ), array( 'min' => 0, 'max' => 10000, 'step' => 1 ) );
		?>
	</div>
	<div class="ca-admin-grid ca-admin-grid-wide">
		<?php
		ca_recipe_render_field( 'ingredients', __( 'Ingredients', 'crisp-and-air-recipes' ), 'textarea', __( 'One ingredient with its exact quantity per line.', 'crisp-and-air-recipes' ), array( 'rows' => 8 ) );
		ca_recipe_render_field( 'instructions', __( 'Step-by-step instructions', 'crisp-and-air-recipes' ), 'textarea', __( 'Enter one instruction step per line. Include practical visual cues and a safe doneness check when relevant.', 'crisp-and-air-recipes' ), array( 'rows' => 8 ) );
		?>
	</div>
	<div class="ca-admin-grid ca-admin-grid-wide">
		<?php
		ca_recipe_render_field( 'tips', __( 'Helpful cooking tips', 'crisp-and-air-recipes' ), 'textarea', __( 'Only recipe-specific advice that helps the reader.', 'crisp-and-air-recipes' ), array( 'rows' => 4 ) );
		ca_recipe_render_field( 'variations', __( 'Variations', 'crisp-and-air-recipes' ), 'textarea', __( 'Relevant variations, one per line.', 'crisp-and-air-recipes' ), array( 'rows' => 4 ) );
		ca_recipe_render_field( 'substitutions', __( 'Useful substitutions', 'crisp-and-air-recipes' ), 'textarea', __( 'Leave empty if a substitution would not work well.', 'crisp-and-air-recipes' ), array( 'rows' => 4 ) );
		ca_recipe_render_field( 'storage', __( 'Storage instructions', 'crisp-and-air-recipes' ), 'textarea', '', array( 'rows' => 3 ) );
		ca_recipe_render_field( 'reheating', __( 'Reheating instructions', 'crisp-and-air-recipes' ), 'textarea', '', array( 'rows' => 3 ) );
		ca_recipe_render_field( 'notes', __( 'Recipe notes', 'crisp-and-air-recipes' ), 'textarea', __( 'Optional notes displayed on the printable card.', 'crisp-and-air-recipes' ), array( 'rows' => 3 ) );
		ca_recipe_render_field( 'faqs', __( 'Frequently asked questions', 'crisp-and-air-recipes' ), 'textarea', __( 'One FAQ per line in this format: Question | Answer. Add only useful, recipe-specific answers.', 'crisp-and-air-recipes' ), array( 'rows' => 5 ) );
		?>
	</div>
	<div class="ca-admin-checkboxes">
		<label><input type="checkbox" name="ca_recipe[featured]" value="1" <?php checked( '1', ca_recipe_meta( $post->ID, 'featured' ) ); ?> /> <?php esc_html_e( 'Feature on the homepage', 'crisp-and-air-recipes' ); ?></label>
		<label><input type="checkbox" name="ca_recipe[popular]" value="1" <?php checked( '1', ca_recipe_meta( $post->ID, 'popular' ) ); ?> /> <?php esc_html_e( 'Include in Popular Recipes', 'crisp-and-air-recipes' ); ?></label>
	</div>
	<?php
}

function ca_recipe_render_seo_box( $post ) {
	ca_recipe_render_field( 'seo_title', __( 'SEO title', 'crisp-and-air-recipes' ), 'text', __( 'Optional. Aim for a natural title; the recipe title is used by default.', 'crisp-and-air-recipes' ), array( 'maxlength' => 70 ) );
	ca_recipe_render_field( 'meta_description', __( 'Meta description', 'crisp-and-air-recipes' ), 'textarea', __( 'Optional. A concise, accurate summary; the excerpt is used by default.', 'crisp-and-air-recipes' ), array( 'rows' => 4, 'maxlength' => 170 ) );
	?>
	<p class="description"><?php esc_html_e( 'Keep the featured image and excerpt accurate. Search engines may choose a different snippet.', 'crisp-and-air-recipes' ); ?></p>
	<?php
}

function ca_recipe_render_checklist_box( $post ) {
	$checks = array(
		__( 'Write an original introduction and answer the cooking question.', 'crisp-and-air-recipes' ),
		__( 'Verify ingredient quantities, air-fryer timing, and safe doneness.', 'crisp-and-air-recipes' ),
		__( 'Choose a useful category and add only relevant tags.', 'crisp-and-air-recipes' ),
		__( 'Upload a rights-cleared image and write descriptive alt text.', 'crisp-and-air-recipes' ),
		__( 'Review the recipe card, links, and SEO description.', 'crisp-and-air-recipes' ),
	);
	?>
	<ul class="ca-publish-checklist">
		<?php foreach ( $checks as $check ) : ?><li><?php echo esc_html( $check ); ?></li><?php endforeach; ?>
	</ul>
	<p class="description"><?php esc_html_e( 'Imported titles stay as drafts until you publish or schedule them.', 'crisp-and-air-recipes' ); ?></p>
	<?php
}

add_action( 'save_post_post', 'ca_recipe_save_meta', 10, 2 );
function ca_recipe_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['ca_recipe_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ca_recipe_details_nonce'] ) ), 'ca_save_recipe_details' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['ca_recipe'] ) || ! is_array( $_POST['ca_recipe'] ) ) {
		return;
	}
	$raw = wp_unslash( $_POST['ca_recipe'] );
	$raw = array_map( static function ( $value ) {
		return is_scalar( $value ) ? (string) $value : '';
	}, $raw );
	$normalized = ca_recipe_normalize_import_meta( $raw );
	foreach ( $normalized as $key => $value ) {
		update_post_meta( $post_id, '_ca_' . $key, $value );
	}
	$allowed_text = array( 'air_fryer_temp', 'servings', 'course', 'cuisine', 'difficulty', 'seo_title', 'meta_description' );
	foreach ( $allowed_text as $key ) {
		if ( ! array_key_exists( $key, $raw ) ) {
			continue;
		}
		$value = sanitize_text_field( $raw[ $key ] );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_ca_' . $key );
		} else {
			update_post_meta( $post_id, '_ca_' . $key, $value );
		}
	}
	$long_text = array( 'ingredients', 'instructions', 'notes', 'tips', 'variations', 'substitutions', 'storage', 'reheating', 'faqs' );
	foreach ( $long_text as $key ) {
		if ( ! array_key_exists( $key, $raw ) ) {
			continue;
		}
		$value = sanitize_textarea_field( $raw[ $key ] );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_ca_' . $key );
		} else {
			update_post_meta( $post_id, '_ca_' . $key, $value );
		}
	}
	foreach ( array( 'prep_minutes', 'cook_minutes', 'calories' ) as $key ) {
		if ( ! array_key_exists( $key, $raw ) ) {
			continue;
		}
		$value = '' === trim( $raw[ $key ] ) ? '' : absint( $raw[ $key ] );
		if ( '' === $value || 0 === $value ) {
			delete_post_meta( $post_id, '_ca_' . $key );
		} else {
			update_post_meta( $post_id, '_ca_' . $key, $value );
		}
	}
	foreach ( array( 'featured', 'popular' ) as $key ) {
		if ( isset( $raw[ $key ] ) && '1' === $raw[ $key ] ) {
			update_post_meta( $post_id, '_ca_' . $key, '1' );
		} else {
			delete_post_meta( $post_id, '_ca_' . $key );
		}
	}
}

add_action( 'show_user_profile', 'ca_recipe_author_profile_fields' );
add_action( 'edit_user_profile', 'ca_recipe_author_profile_fields' );
function ca_recipe_author_profile_fields( $user ) {
	$image_id = absint( get_user_meta( $user->ID, 'ca_author_image_id', true ) );
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	$bio      = get_user_meta( $user->ID, 'ca_author_short_bio', true );
	?>
	<h2><?php esc_html_e( 'Crisp & Air author profile', 'crisp-and-air-recipes' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="ca_author_short_bio"><?php esc_html_e( 'Short author bio', 'crisp-and-air-recipes' ); ?></label></th>
			<td><textarea name="ca_author_short_bio" id="ca_author_short_bio" rows="4" class="regular-text"><?php echo esc_textarea( $bio ); ?></textarea><p class="description"><?php esc_html_e( 'Shown on recipe pages and the author archive. Keep it accurate; do not add unearned credentials.', 'crisp-and-air-recipes' ); ?></p></td>
		</tr>
		<tr>
			<th><label for="ca_author_image_id"><?php esc_html_e( 'Author photo', 'crisp-and-air-recipes' ); ?></label></th>
			<td>
				<div class="ca-author-image-preview" id="ca-author-image-preview"><?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="" /><?php endif; ?></div>
				<input type="hidden" name="ca_author_image_id" id="ca_author_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
				<button type="button" class="button" id="ca-author-image-upload"><?php esc_html_e( 'Choose photo', 'crisp-and-air-recipes' ); ?></button>
				<button type="button" class="button" id="ca-author-image-remove" <?php disabled( ! $image_id ); ?>><?php esc_html_e( 'Remove', 'crisp-and-air-recipes' ); ?></button>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'personal_options_update', 'ca_recipe_save_author_profile_fields' );
add_action( 'edit_user_profile_update', 'ca_recipe_save_author_profile_fields' );
function ca_recipe_save_author_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	if ( isset( $_POST['ca_author_short_bio'] ) ) {
		update_user_meta( $user_id, 'ca_author_short_bio', sanitize_textarea_field( wp_unslash( $_POST['ca_author_short_bio'] ) ) );
	}
	if ( isset( $_POST['ca_author_image_id'] ) ) {
		$image_id = absint( wp_unslash( $_POST['ca_author_image_id'] ) );
		$mime_type = $image_id ? (string) get_post_mime_type( $image_id ) : '';
		if ( $image_id && 0 === strpos( $mime_type, 'image/' ) ) {
			update_user_meta( $user_id, 'ca_author_image_id', $image_id );
		} else {
			delete_user_meta( $user_id, 'ca_author_image_id' );
		}
	}
}
