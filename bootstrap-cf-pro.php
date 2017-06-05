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
		caldera_forms_pro_db_delta_1();
		update_option( 'cf_pro_db_v', 1 );
	}

	//add hooks
	container::get_instance()->get_hooks()->add_hooks();

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
});

/**
 * PSR-4 Autoloader for Caldera Forms Pro
 */
spl_autoload_register(function ($class) {

	// project-specific namespace prefix
	$prefix = 'calderawp\\calderaforms\\pro';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/classes/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

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
	$max_index_length = 191;
	$table = "CREATE TABLE `" . $wpdb->prefix . "cf_pro_messages` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`cfp_id` bigint(20) unsigned DEFAULT NULL,
			`entry_id` bigint(20) unsigned DEFAULT NULL,
			`hash` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`ID`)
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
		define( 'CF_PRO_APP_URL', 'http://app.space.dev' );

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
	$message = apply_filters( 'caldera_forms_pro_link_message', __( 'Download Form Entry As PDF', 'caldera-forms', $form ), $form );

	/**
	 * Filter the title attribute for the generate PDF link HTML
	 *
	 * @param string $title Title attribute.
	 * @param array $form Form config
	 */
	$title = apply_filters( 'caldera_forms_pro_link_title',  __( 'Download Form Entry As PDF', 'caldera-forms' ), $form );

	return sprintf( '<div class="%s"><a href="%s" title="%s" target="_blank">%s</a></div>',
		esc_attr( $classes ),
		esc_url( $link ),
		esc_attr( $title ),
		esc_html( $message )
	);
}