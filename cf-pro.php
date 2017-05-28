<?php
/**
 Plugin Name: Caldera Forms Pro Client
Version: 0.0.1
 */

if( ! defined( 'CF_PRO_VER' ) ){
	/**
	 * Caldera Forms Pro Client Version
	 */
	define( 'CF_PRO_VER', '0.0.1' );
	if( ! defined( 'CF_PRO_APP_URL' ) ){
		/**
		 * Default URL for CF Pro App
		 */
		define( 'CF_PRO_APP_URL', 'http://app.space.dev' );

	}


	include_once  dirname( __FILE__ ) . '/bootstrap-cf-pro.php';
}
