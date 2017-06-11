<?php


namespace calderawp\calderaforms\pro\admin;
use calderawp\calderaforms\pro\container;


/**
 * Class scripts
 * @package calderawp\calderaforms\pro\admin
 */
class scripts {

	/** @var string  */
	protected $assets_url;

	/** @var string  */
	protected $slug;

	/** @var string  */
	protected  $version;

	/**
	 * scripts constructor.
	 *
	 * @param string $assets_url Url for  assets dir
	 * @param string $slug Slug for script/css
	 * @param string $version Current version
	 */
	public function __construct( $assets_url, $slug, $version ){
		$this->assets_url = $assets_url;
		$this->slug = $slug;
		$this->version = $version;

	}

	/**
	 * Register assets
	 *
	 * @uses "admin_enqueue_scripts"
	 *
	 * @since 0.0.1
	 */
	public function register_assets(){
		$vue_slug = \Caldera_Forms_Render_Assets::make_slug( 'vue' );
		if(  \Caldera_Forms_Render_Assets::should_minify() ){
			$js_url = $this->assets_url . 'js/admin.min.js';
			$css_url = $this->assets_url . 'css/admin.css';
		}else {
			$js_url  = $this->assets_url . 'js/admin.js';
			$css_url = $this->assets_url . 'css/admin/admin.css';
		}
		wp_register_style( $this->slug, $css_url, [ 'caldera-forms-admin-styles' ], $this->version );
		wp_register_script( $this->slug, $js_url, array( $vue_slug ), $this->version  );
		wp_localize_script( $this->slug, 'CF_PRO_ADMIN', $this->data() );

	}

	/**
	 * Enqueue assets
	 *
	 * Note: is not hooked.
	 */
	public function enqueue_assets(){
		if( ! wp_script_is( $this->slug, 'registered' ) ){
			$this->register_assets();
		}
		wp_enqueue_script( $this->slug );
		wp_enqueue_style( $this->slug );

	}

	/**
	 * Data to localize
	 *
	 * @return array
	 */
	public function data(){
		$data = array(
			'strings' =>  [
				'saved' => esc_html__( 'Settings Saved', 'caldera-forms-pro' ),
				'notSaved' => esc_html__( 'Settings could not be saved', 'caldera-forms-pro' )
			],
			'api' => array(
				'cf' => array(
					'url' => esc_url_raw( \Caldera_Forms_API_Util::url( 'settings/pro' ) ),
					'nonce'=> wp_create_nonce( 'wp_rest' )
				),
				'cfPro' => array(
					'url' => esc_url_raw( caldera_forms_pro_app_url() ),
					'auth' => array()
				)
			),
			'settings' => container::get_instance()->get_settings()->toArray()
		);
		return $data;
	}
}

class Apex_Settings {

	/**
	 * Option key to save settings
	 *
	 * @var string
	 */
	protected static $option_key = '_apex_settings';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	protected static $defaults = array(
		'industry' => 'lumber',
		'amount' => 42
	);

	/**
	 * Get saved settings
	 *
	 * @return array
	 */
	public static function get_settings(){
		$saved = get_option( self::$option_key, array() );
		if( ! is_array( $saved ) || ! empty( $saved )){
			return self::$defaults;
		}

		return wp_parse_args( $saved, self::$defaults );
	}

	/**
	 * Save settings
	 *
	 * Array keys must be whitelisted (IE must be keys of self::$defaults
	 *
	 * @param array $settings
	 */
	public static function save_settings( array  $settings ){

		//remove any non-allowed indexes before save
		foreach ( $settings as $i => $setting ){
			if( ! array_key_exists( $setting, self::$defaults ) ){
				unset( $settings[ $i ] );
			}
		}

		update_option( self::$option_key, $settings );
	}
}
