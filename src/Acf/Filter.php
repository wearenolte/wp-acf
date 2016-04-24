<?php namespace Lean\Acf;

/**
 * Class that carries the filters and some util function aroudn this,
 *
 * @since 0.4.0
 */
class Filter {
	const DEFAULT_TRANSFORMS = 'ln_acf_apply_default_transforms';
	const FIELD = 'ln_acf_field';

	public static function parse_suffix( $suffix = '' ) {
		$suffix = strtolower( $suffix );
		$suffix = str_replace( [ '-', '/', '.' ], '_', $suffix );
		return trim( $suffix, ' _' );
	}

	public static function create_name( $base = '', $suffix = '' ) {
		if ( $suffix ) {
			return sprintf( '%s_%s', $base, self::parse_suffix( $suffix ) );
		} else {
			return $base;
		}
	}
}
