<?php namespace Lean\Acf\Transforms;

use Lean\Acf\Filter;

/**
 * Class Image.
 *
 * @package Lean\Acf\Transforms
 */
class Image {
	/**
	 * Apply the transforms
	 *
	 * @param array $field The field.
	 * @return array
	 */
	public static function apply( $field ) {
		if ( 'image' === $field['type'] && 'id' === $field['return_format'] ) {
			self::transform_image_fields( $field );
		}

		return $field['value'];
	}

	/**
	 * Do the image size transform.
	 *
	 * @param string $field			Field
	 * @return array
	 */
	public static function transform_image_fields( &$field ) {
		$field['value'] = self::get_image_fields( $field, $field['value'] );
	}

	/**
	 * Get image fields.
	 *
	 * @param string $field			Field
	 * @param int	 $attachment_id	The image id.
	 * @param bool   $sub_field		Sub field (only if it's a repeater)
	 * @return array
	 */
	public static function get_image_fields( $field, $attachment_id, $sub_field = false ) {
		$size = apply_filters( Filter::IMAGE, false, $field, $sub_field );

		if ( ! $size ) {
			return $attachment_id;
		}

		$src = wp_get_attachment_image_src( $attachment_id, $size );

		if ( ! $src ) {
			return $attachment_id;
		}

		return [
			'src' 		=> $src[0],
			'width'		=> $src[1],
			'height'	=> $src[2],
			'alt'		=> get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		];
	}
}
