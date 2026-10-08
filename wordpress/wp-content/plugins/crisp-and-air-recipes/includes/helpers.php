<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ca_recipe_meta' ) ) {
	function ca_recipe_meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( (int) $post_id, '_ca_' . sanitize_key( $key ), true );
		return '' === $value || null === $value ? $default : $value;
	}
}

if ( ! function_exists( 'ca_recipe_lines' ) ) {
	function ca_recipe_lines( $value ) {
		$value = (string) $value;
		$lines = preg_split( '/\r\n|\r|\n/', $value );
		$items = array();
		foreach ( $lines as $line ) {
			$line = trim( wp_strip_all_tags( $line ) );
			$line = preg_replace( '/^\s*(?:\d+\s*[.)-]|[-*•])\s*/u', '', $line );
			if ( '' !== $line ) {
				$items[] = $line;
			}
		}
		return $items;
	}
}

if ( ! function_exists( 'ca_recipe_faq_items' ) ) {
	function ca_recipe_faq_items( $value ) {
		$items = array();
		foreach ( ca_recipe_lines( $value ) as $line ) {
			$parts = explode( '|', $line, 2 );
			if ( 2 !== count( $parts ) ) {
				continue;
			}
			$question = trim( $parts[0] );
			$answer   = trim( $parts[1] );
			if ( '' !== $question && '' !== $answer ) {
				$items[] = array(
					'question' => $question,
					'answer'   => $answer,
				);
			}
		}
		return $items;
	}
}

if ( ! function_exists( 'ca_recipe_iso_duration' ) ) {
	function ca_recipe_iso_duration( $minutes ) {
		$minutes = absint( $minutes );
		return $minutes > 0 ? 'PT' . $minutes . 'M' : '';
	}
}

if ( ! function_exists( 'ca_recipe_display_minutes' ) ) {
	function ca_recipe_display_minutes( $minutes ) {
		$minutes = absint( $minutes );
		if ( $minutes < 60 ) {
			return sprintf( _n( '%s min', '%s mins', $minutes, 'crisp-and-air-recipes' ), number_format_i18n( $minutes ) );
		}
		$hours      = (int) floor( $minutes / 60 );
		$remainder  = $minutes % 60;
		$parts      = array( sprintf( _n( '%s hr', '%s hrs', $hours, 'crisp-and-air-recipes' ), number_format_i18n( $hours ) ) );
		if ( $remainder ) {
			$parts[] = sprintf( _n( '%s min', '%s mins', $remainder, 'crisp-and-air-recipes' ), number_format_i18n( $remainder ) );
		}
		return implode( ' ', $parts );
	}
}

if ( ! function_exists( 'ca_recipe_author_photo' ) ) {
	function ca_recipe_author_photo( $user_id, $size = 'thumbnail' ) {
		$image_id = absint( get_user_meta( (int) $user_id, 'ca_author_image_id', true ) );
		if ( ! $image_id ) {
			return '';
		}
		return wp_get_attachment_image( $image_id, $size, false, array( 'class' => 'author-photo', 'alt' => '' ) );
	}
}

if ( ! function_exists( 'ca_recipe_author_bio' ) ) {
	function ca_recipe_author_bio( $user_id ) {
		$bio = get_user_meta( (int) $user_id, 'ca_author_short_bio', true );
		if ( '' === trim( (string) $bio ) ) {
			$bio = get_the_author_meta( 'description', (int) $user_id );
		}
		return $bio ? wp_strip_all_tags( $bio ) : '';
	}
}

/** Normalize supported CSV recipe fields before they are stored. */
function ca_recipe_normalize_import_meta( $row ) {
	$text_fields = array(
		'air_fryer_temp' => 'air_fryer_temp',
		'servings'      => 'servings',
		'course'        => 'course',
		'cuisine'       => 'cuisine',
		'difficulty'    => 'difficulty',
		'ingredients'   => 'ingredients',
		'instructions'  => 'instructions',
		'notes'         => 'notes',
		'tips'          => 'tips',
		'variations'    => 'variations',
		'substitutions' => 'substitutions',
		'storage'       => 'storage',
		'reheating'     => 'reheating',
		'faqs'          => 'faqs',
		'seo_title'     => 'seo_title',
		'meta_description' => 'meta_description',
	);
	$normalized = array();
	foreach ( $text_fields as $column => $meta_key ) {
		if ( isset( $row[ $column ] ) && '' !== trim( (string) $row[ $column ] ) ) {
			if ( in_array( $meta_key, array( 'ingredients', 'instructions', 'notes', 'tips', 'variations', 'substitutions', 'storage', 'reheating', 'faqs' ), true ) ) {
				$normalized[ $meta_key ] = sanitize_textarea_field( (string) $row[ $column ] );
			} else {
				$normalized[ $meta_key ] = sanitize_text_field( (string) $row[ $column ] );
			}
		}
	}
	foreach ( array( 'prep_minutes', 'cook_minutes', 'calories' ) as $numeric_key ) {
		if ( isset( $row[ $numeric_key ] ) && '' !== trim( (string) $row[ $numeric_key ] ) && is_numeric( $row[ $numeric_key ] ) ) {
			$normalized[ $numeric_key ] = absint( $row[ $numeric_key ] );
		}
	}
	return $normalized;
}

function ca_recipe_store_import_meta( $post_id, $row ) {
	$meta = ca_recipe_normalize_import_meta( $row );
	foreach ( $meta as $key => $value ) {
		update_post_meta( (int) $post_id, '_ca_' . $key, $value );
	}
	if ( isset( $row['featured'] ) && in_array( strtolower( trim( (string) $row['featured'] ) ), array( '1', 'yes', 'true', 'y' ), true ) ) {
		update_post_meta( (int) $post_id, '_ca_featured', '1' );
	}
	if ( isset( $row['popular'] ) && in_array( strtolower( trim( (string) $row['popular'] ) ), array( '1', 'yes', 'true', 'y' ), true ) ) {
		update_post_meta( (int) $post_id, '_ca_popular', '1' );
	}
}
