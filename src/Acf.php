<?php namespace Leean;

use Leean\Acf\Filter;

/**
 * Class to provide helpers for getting the ACF fields for an entity.
 */
class Acf {
	/**
	 * Is the ACF plugin active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return function_exists( 'get_field_object' );
	}

	/**
	 * Get the field value for a post.
	 *
	 * @param int $post_id The target post's id. Or leave blank for he current post if in the loop.
	 * @param bool $include_wp_fields Whether or not to include the default WP fields.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return array
	 */
	public static function get_post_field( $post_id = 0, $include_wp_fields = true, $field = '' ) {
		if ( $include_wp_fields && ! $field ) {
			$wp_fields = [
				'post_id' => $post_id,
				'permalink' => get_permalink( $post_id ),
				'title' => get_the_title( $post_id ),
				'content' => apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ),
			];
			return array_merge( $wp_fields, self::get_field( $post_id ) );
		}
		return self::get_field( $post_id, $field );
	}

	/**
	 * Get the fields for a comment.
	 *
	 * @param int|\WP_Comment $comment The target comment's id or object.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 */
	public static function get_comment_field( $comment, $field = '' ) {
		return self::get_field( is_a( $comment, 'WP_Comment' ) ? $comment : "comment_{$comment}", $field );
	}

	/**
	 * Get the fields for an attachment.
	 *
	 * @param int $attachment_id The target attachment's id.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 */
	public static function get_attachment_field( $attachment_id, $field = '' ) {
		return self::get_field( $attachment_id, $field );
	}

	/**
	 * Get the fields for a taxonomy term.
	 *
	 * @param array|\WP_Term $taxonomy_term The target term's [taxonomy, $term_id] or term object.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 * @throws \Exception
	 */
	public static function get_taxonomy_field( $taxonomy_term, $field = '' ) {
		if ( is_a( $taxonomy_term, 'WP_Term' ) ) {
			return self::get_field( $taxonomy_term, $field );
		} elseif ( is_array( $taxonomy_term ) && count( $taxonomy_term ) >= 2 ) {
			return self::get_field( "{$taxonomy_term[0]}_{$taxonomy_term[1]}", $field );
		}
		throw new \Exception( '$taxonomy_term must be either a term object or an array of [$taxonomy, $term_id]' );
	}

	/**
	 * Get the fields for a user.
	 *
	 * @param int $user_id The target user's id.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 */
	public static function get_user_field( $user_id, $field = '' ) {
		return self::get_field( "user_{$user_id}", $field );
	}

	/**
	 * Get the fields for a widget.
	 *
	 * @param string $widget_id The target widget's id.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 */
	public static function get_widget_field( $widget_id, $field = '' ) {
		return self::get_field( "widget_{$widget_id}", $field );
	}

	/**
	 * Get option fields.
	 *
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return mixed
	 */
	public static function get_option_field( $field = '' ) {
		return self::get_field( 'option', $field );
	}

	/**
	 * Get all field values.
	 *
	 * @param int $target_id The target object's id.
	 * @param string $field The ACF field id or name. Return all fields if blank.
	 * @return array
	 */
	private static function get_field( $target_id = 0, $field = '' ) {
		if ( self::is_active() ) {
			if ( $field ) {
				return self::get_single_field( $target_id, $field );
			} else {
				return self::get_all_fields( $target_id );
			}
		}
		return $field ? null : [];
	}

	private static function get_all_fields( $target_id = 0, $field = '' ) {
		$data = [];

		$field_objs = get_field_objects( $target_id );

		if ( $field_objs ) {
			foreach ( $field_objs as $field_name => $field_obj ) {
				$value = self::get_single_field( $target_id, $field_obj );

				$parent = get_post( $field_obj['parent'] );

				if ( $parent ) {
					$data[ $parent->post_excerpt ][ $field_name ] = $value;
				} else {
					$data[ $field_name ] = $value;
				}
			}
		}

		return $data;
	}

	private static function get_single_field( $target_id = 0, $field = '' ) {
		$field_obj = is_array( $field ) ? $field :  get_field_object( $field, $target_id );

		$field_key = isset( $field_obj['key'] ) ? $field_obj['key'] : '';
		$filter_name = Filter::create_name( Filter::DEFAULT_TRANSFORMS, $field_key );
		$apply_default_transforms = apply_filters( $field_name, $target_id, $field_obj );

		$value = $apply_default_transforms ? self::apply_default_transform( $field_obj ) : $field_obj['value'];

		$filter_name = Filter::create_name( Filter::FIELD, $field_key );
		return apply_filters( 'ln_acf_field', $value, $target_id, $field_obj );
	}

	/**
	 * Apply the default transforms if any exist for this field type.
	 *
	 * @param array $field The field.
	 * @return mixed
	 */
	private static function apply_default_transform( $field_obj ) {
		$class = self::get_transform_class_name( $field_obj['type'] );

		if ( method_exists( $class, 'apply' ) ) {
			return call_user_func( [ $class, 'apply' ], $field_obj );
		}

		return $field_obj['value'];
	}

	/**
	 * Return the class name for the transforms of this field type.
	 *
	 * @param string $field_type The ACF type of the field.
	 * @return string
	 */
	private static function get_transform_class_name( $field_type ) {
		return '\\' . __NAMESPACE__ . '\\Acf\\Transforms\\' . str_replace( '_', '', ucwords( $field_type, '_' ) );
	}
}
