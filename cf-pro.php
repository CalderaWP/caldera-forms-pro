<?php
/**
 Plugin Name: Caldera Forms Pro Client
Version: 0.3.0
 */



if ( ! version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
	add_action( 'admin_notices', 'caldera_forms_pro_version_fail_warning' );
	function caldera_forms_pro_version_fail_warning(){
		$class = 'notice notice-error';
		$message = __( 'Caldera Forms Pro could not be loaded because your PHP is out of date.', 'cf-pro' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}


}else{
	if( ! defined( 'CF_PRO_VER' ) ){

		/**
		 * Define Plugin basename for updater
		 *
		 * @since 0.2.0
		 */
		define( 'CF_PRO_BASENAME', plugin_basename(__FILE__) );

		/**
		 * Caldera Forms Pro Client Version
		 */
		define( 'CF_PRO_VER', '0.3.0' );

		include_once  dirname( __FILE__ ) . '/bootstrap-cf-pro.php';
	}

}
