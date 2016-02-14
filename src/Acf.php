<?php namespace Leean;

/**
 * Class to provide helpers for working with the ACF plugin.
 */
class Acf {
	public static function is_active() {
		return function_exists( 'get_field_object' );
	}

	private static function get_field( $field, $target = 0 ) {
		if ( self::is_active() ) {
			$field_obj = get_field_object( $field, $target );

			if ( $field_obj ) {
				if ( $field_obj['type'] === 'repeater' ) {
					return $field_obj['value'] === false ? [] : $field_obj['value'];
				}
				return $field_obj['value'];
			}
		}

		return null;
	}

	public static function get_post_field( $field, $post_id = 0 ) {
		return self::get_field( $field, $post_id );
	}

	public static function get_comment_field( $field, $comment ) {
		return self::get_field( $field, is_a( $comment, 'WP_Comment' ) ? $comment : "comment_{$comment}" );
	}

	public static function get_attachment_field( $field, $attachment_id ) {
		return self::get_field( $field, $attachment_id );
	}

	public static function get_taxonomy_field( $field, $taxonomy_term ) {
		if ( is_a( $taxonomy_term, 'WP_Term' ) ) {
			return self::get_field( $field, $taxonomy_term );
		} elseif ( is_array( $taxonomy_term ) && count( $taxonomy_term ) >= 2 ) {
			return self::get_field( $field, "{$taxonomy_term[0]}_{$taxonomy_term[1]}" );
		}
		throw new \Exception( '$taxonomy_term must be either a term object or an array of [$taxonomy, $term_id]' );
	}

	public static function get_user_field( $field, $user_id ) {
		return self::get_field( $field, "user_{$user_id}" );
	}

	public static function get_widget_field( $field, $widget_id ) {
		return self::get_field( $field, "widget_{$widget_id}" );
	}

	public static function get_option_field( $field ) {
		return self::get_field( $field, 'option' );
	}
}
