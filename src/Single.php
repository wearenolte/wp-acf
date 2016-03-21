<?php namespace Leean\Acf;

/**
 * Class to provide helpers for getting a single ACF field.
 */
class Single
{
	/**
	 * Is the ACF plugin active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return function_exists( 'get_field_object' );
	}

	/**
	 * Get the field value.
	 * If it's a repeater return [] instead of false if empty.
	 *
	 * @param string $field Field key or name.
	 * @param int $target The target object.
	 * @return mixed
	 */
	private static function get_field( $field, $target = 0 ) {
		if ( self::is_active() ) {
			$field_obj = get_field_object( $field, $target );

			if ( $field_obj ) {
				if ( 'repeater' === $field_obj['type'] ) {
					return false === $field_obj['value'] ? [] : $field_obj['value'];
				}
				return $field_obj['value'];
			}
		}

		return null;
	}

	/**
	 * Get the field value for a post.
	 *
	 * @param string $field Field key or name.
	 * @param int $post_id The target post's id. Or leave blank for he current post if in the loop.
	 * @return mixed
	 */
	public static function get_post_field( $field, $post_id = 0 ) {
		return self::get_field( $field, $post_id );
	}

	/**
	 * Get the field value for a comment.
	 *
	 * @param string $field Field key or name.
	 * @param int|\WP_Comment $comment The target comment's id or object.
	 * @return mixed
	 */
	public static function get_comment_field( $field, $comment ) {
		return self::get_field( $field, is_a( $comment, 'WP_Comment' ) ? $comment : "comment_{$comment}" );
	}

	/**
	 * Get the field value for an attachment.
	 *
	 * @param string $field Field key or name.
	 * @param int $attachment_id The target attachment's id.
	 * @return mixed
	 */
	public static function get_attachment_field( $field, $attachment_id ) {
		return self::get_field( $field, $attachment_id );
	}

	/**
	 * Get the field value for a taxonomy term.
	 *
	 * @param string $field Field key or name.
	 * @param array|\WP_Term $taxonomy_term The target term's [taxonomy, $term_id] or term object.
	 * @return mixed
	 * @throws \Exception
	 */
	public static function get_taxonomy_field( $field, $taxonomy_term ) {
		if ( is_a( $taxonomy_term, 'WP_Term' ) ) {
			return self::get_field( $field, $taxonomy_term );
		} elseif ( is_array( $taxonomy_term ) && count( $taxonomy_term ) >= 2 ) {
			return self::get_field( $field, "{$taxonomy_term[0]}_{$taxonomy_term[1]}" );
		}
		throw new \Exception( '$taxonomy_term must be either a term object or an array of [$taxonomy, $term_id]' );
	}

	/**
	 * Get the field value for a user.
	 *
	 * @param string $field Field key or name.
	 * @param int $user_id The target user's id.
	 * @return mixed
	 */
	public static function get_user_field( $field, $user_id ) {
		return self::get_field( $field, "user_{$user_id}" );
	}

	/**
	 * Get the field value for a widget.
	 *
	 * @param string $field Field key or name.
	 * @param int $widget_id The target widget's id.
	 * @return mixed
	 */
	public static function get_widget_field( $field, $widget_id ) {
		return self::get_field( $field, "widget_{$widget_id}" );
	}

	/**
	 * Get the field value for an option.
	 *
	 * @param string $field Field key or name.
	 * @return mixed
	 */
	public static function get_option_field( $field ) {
		return self::get_field( $field, 'option' );
	}
}
