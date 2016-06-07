<?php namespace Lean\Acf;

use Lean\Acf;

/**
 * Output ACF fields in the WP Rest API.
 */
class RestApi {

	const FIELD_NAME = 'acf';

	/**
	 * Initiate the ACF Rest API functionality.
	 */
	public static function init() {
		if ( function_exists('register_rest_field') ) {
			add_action( 'rest_api_init', [ __CLASS__, 'register' ] );
		}
	}

	/**
	 * Register the field for all post types and users.
	 */
	public static function register() {
		foreach ( [ 'post', 'user' ] as $type ) {
			register_rest_field(
				'post' === $type ? get_post_types() : $type ,
				self::FIELD_NAME,
				[ 'get_callback' => [ __CLASS__, 'get_' . $type . '_fields' ] ]
			);
		}
	}

	/**
	 * Get all ACF fields for the post.
	 *
	 * @param array 		   $object     The current object.
	 * @param string 		   $field_name The field name.
	 * @param \WP_REST_Request $request    The request.
	 * @return array
	 */
	public static function get_post_fields( $object, $field_name, $request ) {
		return Acf::get_post_field( $object['id'], false );
	}

	/**
	 * Get all ACF fields for the user.
	 *
	 * @param array 		   $object     The current object.
	 * @param string 		   $field_name The field name.
	 * @param \WP_REST_Request $request    The request.
	 * @return array
	 */
	public static function get_user_fields( $object, $field_name, $request ) {
		return Acf::get_user_field( $object['id'] );
	}
}
