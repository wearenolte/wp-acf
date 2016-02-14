<?php namespace Leean;

/**
 * Class to provided helpers for working with the ACF plugin.
 */
class Acf {
	public static function is_active() {
		return function_exists( 'get_field' ) && function_exists( 'the_field' );
	}

	private static function get_field( $field_name, $target = 0, $default_value = false, $format_value = true ) {
		if ( self::is_active() ) {
			return get_field( $field_name, $target, $format_value );
		}
		return $default_value;
	}

	public static function get_post_field( $field_name, $post_id = 0, $default_value = false, $format_value = true ) {
		return self::get_field( $field_name, $post_id, $default_value, $format_value );
	}

	public static function get_comment_field( $field_name, $comment, $default_value = false, $format_value = true ) {
		$target = is_object( $comment ) ? $comment : "comment_{$comment}";
		return self::get_field( $field_name, $target, $default_value, $format_value );
	}

	public static function get_attachment_field( $field_name, $attachment_id, $default_value = false, $format_value = true ) {
		return self::get_field( $field_name, $attachment_id, $default_value, $format_value );
	}

	public static function get_taxonomy_field( $field_name, $taxonomy_term, $default_value = false, $format_value = true ) {
		if ( is_object( $taxonomy_term ) ) {
			return self::get_field( $field_name, $taxonomy_term, $default_value, $format_value );
		} elseif ( is_array( $taxonomy_term ) ) {
			return self::get_field( $field_name, "{$taxonomy_term[0]}_{$taxonomy_term[1]}", $default_value, $format_value );
		}
		throw new \Exception( '$taxonomy_term must be either a term object or an array of [$taxonomy, $term_id]' );
	}

	public static function get_user_field( $field_name, $user_id, $default_value = false, $format_value = true ) {
		return self::get_field( $field_name, "user_{$user_id}", $default_value, $format_value );
	}

	public static function get_widget_field( $field_name, $widget_id, $default_value = false, $format_value = true ) {
		return self::get_field( $field_name, "widget_{$widget_id}", $default_value, $format_value );
	}

	public static function get_option_field( $field_name, $default_value = false, $format_value = true ) {
		return self::get_field( $field_name, 'option', $default_value, $format_value );
	}
}
