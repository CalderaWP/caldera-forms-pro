<?php
use \calderawp\calderaforms\pro\container;
use \calderawp\calderaforms\pro\admin\scripts;
use \calderawp\calderaforms\pro\admin\menu;
/**
 * Load up Caldera Forms Pro API client
 *
 * @since 0.0.1
 */
add_action( 'caldera_forms_includes_complete', function(){
	//add database table if needed
	if( 1 > get_option( 'cf_pro_db_v', 0 ) ){
		caldera_forms_pro_drop_tables();
		caldera_forms_pro_db_delta_1();
		update_option( 'cf_pro_db_v', 1 );
	}

	include_once __DIR__ .'/vendor/autoload.php';

	//add menu page
	if ( is_admin() ) {
		$slug       = 'cf-pro';
		$assets_url = plugin_dir_url( __FILE__  ) . 'assets/';
		$assets_dir = dirname( __FILE__ )  . '/assets/';
		$scripts = new scripts( $assets_url, $slug, CF_PRO_VER );
		add_action( 'admin_enqueue_scripts', [ $scripts, 'register_assets' ] );
		$menu = new menu( $assets_dir . 'templates', $slug, $scripts);
		add_action( 'admin_menu', [ $menu, 'display' ] );
	}

	//add hooks
	container::get_instance()->get_hooks()->add_hooks();

	/**
	 * Runs after Caldera Forms Pro is loaded
	 *
	 * @since 0.5.0
	 */
	do_action( 'caldera_forms_pro_loaded' );

});


/**
 * Delete CF Pro DB Table
 */
function caldera_forms_pro_drop_tables(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_pro_messages';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	delete_option('cf_pro_db_v');
}

/**
 * Database changes for Caldera Forms Pro
 *
 * @since 0.0.1
 */
function caldera_forms_pro_db_delta_1(){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}
	$table = "CREATE TABLE `" . $wpdb->prefix . "cf_pro_messages` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`cfp_id` bigint(20) unsigned DEFAULT NULL,
			`entry_id` bigint(20) unsigned DEFAULT NULL,
			`hash` varchar(255) DEFAULT NULL,
			`type` varchar(255) DEFAULT NULL,
			PRIMARY KEY ( `ID` )
			) " . $charset_collate . ";";

	dbDelta( $table );


}

/**
 * Get the URL for the Caldera Forms Pro App
 *
 * @return string
 */
function caldera_forms_pro_app_url(){

	if( ! defined( 'CF_PRO_APP_URL' ) ){
		/**
		 * Default URL for CF Pro App
		 */
		define( 'CF_PRO_APP_URL', 'https://app.calderaformspro.com' );

	}

	/**
	 * Filter URL for Caldera Forms Pro app
	 *
	 * Useful for local dev or running your own instance of app
	 *
	 * @since 0.0.1
	 *
	 * @param string $url The root URL for app
	 */
	return untrailingslashit( apply_filters( 'caldera_forms_pro_app_url', CF_PRO_APP_URL ) );
}


/**
 * Create HTML for linl
 *
 * @param array $form Form config
 * @param string $link The actual link.
 *
 * @return string
 */
function caldera_forms_pro_link_html( $form, $link ){

	/**
	 * Filter the classes for the generate PDF link HTML
	 *
	 * @param string $classes The classes as string.
	 * @param array $form Form config
	 */
	$classes = apply_filters( 'caldera_forms_pro_link_classes', ' alert alert-success', $form );


	/**
	 * Filter the visible content for the generate PDF link HTML
	 *
	 * @param string $message Link message
	 * @param array $form Form config
	 */
	$message = apply_filters( 'caldera_forms_pro_link_message', __( 'Download Form Entry As PDF', 'caldera-forms-pro', $form ), $form );

	/**
	 * Filter the title attribute for the generate PDF link HTML
	 *
	 * @param string $title Title attribute.
	 * @param array $form Form config
	 */
	$title = apply_filters( 'caldera_forms_pro_link_title',  __( 'Download Form Entry As PDF', 'caldera-forms-pro' ), $form );

	return sprintf( '<div class="%s"><a href="%s" title="%s" target="_blank">%s</a></div>',
		esc_attr( $classes ),
		esc_url( $link ),
		esc_attr( $title ),
		esc_html( $message )
	);
}


if( ! function_exists( 'caldera_forms_safe_explode' ) ){
	function caldera_forms_safe_explode( $string ){
		if( false === strpos( $string, ',' ) ){
			return array( $string );
		}
		return explode(',', $string );
	}
}

/**
 * Add updating via Github
 *
 * @since 0.2.0
 */
add_action( 'init', function(){
	/**
	 * Disable updater
	 *
	 * @since 0.4.0
	 *
	 * @param bool $disable Use __return_true to disable updates via Github
	 */
	if( apply_filters( 'caldera_forms_pro_disable_updater', false ) ){
		return;
	}

	if ( is_admin() ) {
		include_once  __DIR__ . '/updater.php';

		$config = array(
			'slug'               => CF_PRO_BASENAME,
			'proper_folder_name' => 'caldera-forms-pro',
			// this is the name of the folder your plugin lives in
			'api_url'            => 'https://api.github.com/repos/CalderaWP/caldera-forms-pro',
			'raw_url'            => 'https://raw.github.com/CalderaWP/caldera-forms-pro/master',
			'github_url'         => 'https://github.com/CalderaWP/caldera-forms-pro',
			// the GitHub url of your GitHub repo
			'zip_url'            => 'https://github.com/CalderaWP/caldera-forms-pro/archive/master.zip',
			'sslverify'          => true,
			'requires'           => '4.7',
			'tested'             => '4.8',
			'readme'             => 'README.md',
			'version'            => '0.6.0'
		);
		new WP_GitHub_Updater($config);
	}
});


/**
 * Shim for boolval in PHP v5.5
 *
 * @since 0.3.1
 */
if ( ! function_exists( 'boolval' ) ) {
	function boolval( $val ){
		return (bool) $val;

	}

}


