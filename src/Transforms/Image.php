<?php namespace Leean\Acf\Transforms;

/**
 * Class Image.
 *
 * @package Leean\Acf\Transforms
 */
class Image
{
	/**
	 * Apply the transforms
	 *
	 * @param array $field The field.
	 * @return array
	 */
	public static function apply( $field ) {
		if ( ! ( 'image' === $field['type'] && 'id' === $field['return_format']) ) {
			return $field['value'];
		}

		return self::transform_image_fields( $field );
	}

	/**
	 * Do the image size transform.

	 * @param string $field			Field
	 * @param bool   $sub_field		Sub field (only if it's a repeater)
	 * @return array
	 */
	private static function transform_image_fields( $field, $sub_field = false ) {
		$size = apply_filters( 'ln_acf_image_size', false, $field, $sub_field );

		if ( ! $size ) {
			return $field['value'];
		}

		$src = wp_get_attachment_image_src( $field['value'], $size );

		if ( ! $src ) {
			return $field['value'];
		}

		return [
			'src' 		=> $src[0],
			'width'		=> $src[1],
			'height'	=> $src[2],
			'alt'		=> get_post_meta( $field['value'], '_wp_attachment_image_alt', true ),
		];
	}
}
