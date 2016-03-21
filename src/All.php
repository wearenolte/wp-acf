<?php namespace Leean\Acf;

/**
 * Class to provide helpers for getting a all ACF fields for an entity.
 */
class All
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
	 * Apply the default transforms if any exist for this field type.
	 *
	 * @param array $field The field.
	 * @return mixed
	 */
	private static function apply_default_transform( $field ) {
		$class = '\\' . __NAMESPACE__ . '\\Transforms\\' . str_replace( '_', '', ucwords( $field['type'], '_' ) );

		if ( method_exists( $class, 'apply' ) ) {
			return call_user_func( [ $class, 'apply' ], $field );
		}

		return $field['value'];
	}

	/**
	 * Get all field values.
	 *
	 * @param int $target The target object.
	 * @return array
	 */
	private static function get_fields( $target = 0 ) {
		$data = [];

		if ( self::is_active() ) {
			$fields = get_field_objects( $target );

			if ( $fields ) {
				foreach ( $fields as $field_name => $field ) {
					$apply_default_transforms =
						apply_filters( 'ln_acf_apply_default_transforms', true, $target, $field );

					$value = $apply_default_transforms ? self::apply_default_transform( $field ) : $field['value'];

					$parent = get_post( $field['parent'] );

					if ( $parent ) {
						$data[ $parent->post_excerpt ][ $field_name ] =
							apply_filters( 'ln_acf_field', $value, $target, $field );
					} else {
						$data[ $field_name ] =
							apply_filters( 'ln_acf_field', $value, $target, $field );
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Get the field value for a post.
	 *
	 * @param int $post_id The target post's id. Or leave blank for he current post if in the loop.
	 * @param bool $include_wp_fields Whether or not to include the default WP fields.
	 * @return array
	 */
	public static function get_post_fields( $post_id = 0, $include_wp_fields = true ) {
		if ( $include_wp_fields ) {
			$wp_fields = [
				'post_id' => $post_id,
				'permalink' => get_permalink( $post_id ),
				'title' => get_the_title( $post_id ),
				'content' => apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ),
			];

			return array_merge( $wp_fields, self::get_fields( $post_id ) );
		}

		return self::get_fields( $post_id );
	}

	/**
	 * Get the fields for a comment.
	 *
	 * @param int|\WP_Comment $comment The target comment's id or object.
	 * @return mixed
	 */
	public static function get_comment_fields( $comment ) {
		return self::get_fields( is_a( $comment, 'WP_Comment' ) ? $comment : "comment_{$comment}" );
	}

	/**
	 * Get the fields for an attachment.
	 *
	 * @param int $attachment_id The target attachment's id.
	 * @return mixed
	 */
	public static function get_attachment_fields( $attachment_id ) {
		return self::get_fields( $attachment_id );
	}

	/**
	 * Get the fields for a taxonomy term.
	 *
	 * @param array|\WP_Term $taxonomy_term The target term's [taxonomy, $term_id] or term object.
	 * @return mixed
	 * @throws \Exception
	 */
	public static function get_taxonomy_fields( $taxonomy_term ) {
		if ( is_a( $taxonomy_term, 'WP_Term' ) ) {
			return self::get_fields( $taxonomy_term );
		} elseif ( is_array( $taxonomy_term ) && count( $taxonomy_term ) >= 2 ) {
			return self::get_fields( "{$taxonomy_term[0]}_{$taxonomy_term[1]}" );
		}
		throw new \Exception( '$taxonomy_term must be either a term object or an array of [$taxonomy, $term_id]' );
	}

	/**
	 * Get the fields for a user.
	 *
	 * @param int $user_id The target user's id.
	 * @return mixed
	 */
	public static function get_user_fields( $user_id ) {
		return self::get_fields( "user_{$user_id}" );
	}

	/**
	 * Get the fields for a widget.
	 *
	 * @param string $widget_id The target widget's id.
	 * @return mixed
	 */
	public static function get_widget_fields( $widget_id ) {
		return self::get_fields( "widget_{$widget_id}" );
	}

	/**
	 * Get option fields.
	 *
	 * @return mixed
	 */
	public static function get_option_fields() {
		return self::get_fields( 'option' );
	}
}
