<?php namespace Lean\Acf\Transforms;

/**
 * Class Repeater.
 *
 * @package Lean\Acf\Transforms
 */
class Repeater {
	/**
	 * Apply the transforms
	 *
	 * @param array $field The field.
	 * @return array
	 */
	public static function apply( $field ) {
		if ( 'repeater' === $field['type'] && is_array( $field['value'] ) ) {
			self::transform_image_fields( $field );

			self::transform_to_object( $field );
		}

		return $field['value'];
	}

	/**
	 * Do the image size transform for an image sub fields.
	 *
	 * @param string $field			Field.
	 * @return array
	 */
	public static function transform_image_fields( &$field ) {
		if ( empty( $field['value'] ) ) {
			return $field['value'];
		}

		foreach ( $field['sub_fields'] as $sub_field ) {
			if ( 'image' === $sub_field['type'] && 'id' === $sub_field['return_format'] ) {
				foreach ( $field['value'] as $id => $item ) {
					$field['value'][ $id ][ $sub_field['name'] ] =
						Image::get_image_fields( $field, $item[ $sub_field['name'] ], $sub_field['name'] );
				}
			}
		}
	}

	/**
	 * Transform to an object if the filter is set and there is only 1 item.
	 *
	 * @param array $field Field.
	 * @return mixed
	 */
	public static function transform_to_object( &$field ) {
		if ( 1 !== count( $field['value'] ) ) {
			return $field['value'];
		}

		$as_array = ! ( 1 === $field['min'] && 1 === $field['max'] );
		$as_array = apply_filters( 'ln_acf_repeater_as_array', $as_array, $field );

		$field['value'] = $as_array ? $field['value'] : $field['value'][0];
	}
}
