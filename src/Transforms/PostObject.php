<?php namespace Leean\Acf\Transforms;

use Leean\Acf\All;

/**
 * Class PostObject.
 *
 * @package Leean\Acf\Transforms
 */
class PostObject
{
	/**
	 * Apply the transforms
	 *
	 * @param array $field The field.
	 * @return array
	 */
	public static function apply( $field ) {
		if ( ! ( 'post_object' === $field['type'] && 'id' === $field['return_format'] ) ) {
			return $field['value'];
		}

		return self::transform_sub_post_fields( $field );
	}

	/**
	 * Transform to get the WP and ACF fields of the sub post.
	 *
	 * @param array $field The field.
	 * @return array
	 */
	public static function transform_sub_post_fields( $field ) {
		if ( is_array( $field['value'] ) ) {
			$data = [];
			foreach ( $field['value'] as $post_id ) {
				$data[] = All::get_post_fields( $post_id );
			}
			return $data;
		} else {
			return All::get_post_fields( $field['value'] );
		}
	}
}
