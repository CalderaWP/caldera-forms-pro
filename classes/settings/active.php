<?php


namespace calderawp\calderaforms\pro\settings;


/**
 * Class active
 * @package calderawp\calderaforms\pro\settings
 */
class active {

	/**
	 * Setting to store active flag in
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected static $setting = '_cf_pro_is_active';

	/**
	 * Check if CF Pro API is active for this site or not
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public static function get_status(){
		return (bool) apply_filters( 'caldera_forms_pro_is_active', get_option( self::$setting, false ) );
	}

	/**
	 * Change active status
	 *
	 * @since 0.2.0
	 *
	 * @param bool $enable Enable (true) or disable (false) CF Pro API connection
	 */
	public static function change_status( $enable ){
		update_option( self::$setting, (bool) $enable );
	}
}