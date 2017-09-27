<?php
/**
 * Plugin Name: Caldera Forms Pro Client
 *
 * Plugin URI: https://CalderaForms.com/pro
 * Description: Client plugin for the Caldera Forms Pro app.
 * Author: Caldera Labs
 * Author URI: http://CalderaLabs.org
 * Version: 0.11.2
 */


add_action( 'plugins_loaded', 'caldera_forms_pro_init', 5 );
function caldera_forms_pro_init(){
	if ( ! version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
		add_action( 'admin_notices', 'caldera_forms_pro_version_fail_warning' );
		function caldera_forms_pro_version_fail_warning()
		{
			$class   = 'notice notice-error';
			$message = __( 'Caldera Forms Pro could not be loaded because your PHP is out of date. Caldera Forms Pro requires PHP 5.6 or later.', 'cf-pro' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}


	} elseif ( ! defined( 'CFCORE_VER' ) || ! version_compare( CFCORE_VER, '1.5.1', '>=' ) ) {
		add_action( 'admin_notices', 'caldera_forms_pro_cf_version_fail_warning' );
		function caldera_forms_pro_cf_version_fail_warning()
		{
			$class   = 'notice notice-error';
			$message = __( 'Caldera Forms Pro could not be loaded because Caldera Forms is not installed or is out of date.', 'cf-pro' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}
	} else {
		if ( ! defined( 'CF_PRO_VER' ) ) {

			/**
			 * Define Plugin basename for updater
			 *
			 * @since 0.2.0
			 */
			define( 'CF_PRO_BASENAME', plugin_basename( __FILE__ ) );

			/**
			 * Caldera Forms Pro Client Version
			 */
			define( 'CF_PRO_VER', '0.11.2' );

			include_once dirname( __FILE__ ) . '/bootstrap-cf-pro.php';


			register_activation_hook( __FILE__, 'caldera_forms_pro_activation_hook_callback' );

		}

	}
}
