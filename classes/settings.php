<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\api\keys;
use calderawp\calderaforms\pro\settings\form;


/**
 * Class settings
 *
 * Handles CF Pro settings
 *
 * @package calderawp\calderaforms\pro
 */
class settings  extends repository{

	/**
	 * ID of default layout
	 *
	 * @var int
	 */
	protected $default_layout;

	/**
	 * Option key for storage
	 *
	 * @var string
	 */
	protected $option_key = '_cf_pro_settings';

	/**
	 * Option key for storage
	 *
	 * @var string
	 */
	protected static $_option_key = '_cf_pro_settings';

	/**
	 * CF Pro Account ID
	 *
	 * @var int
	 */
	protected $account_id;


	/**
	 * Create object from saved data
	 *
	 * @return settings
	 */
	public static function from_saved(){
		$settings = new static();
		$saved = get_option( self::$_option_key, array( ) );
		if( ! empty( $saved[ 'account_id' ] ) ){
			$settings->set_account_id( $saved[ 'account_id' ]  );
		}
		if( ! empty( $saved[ 'apiKeys' ] ) ){
			$keys = keys::fromArray( $saved[ 'apiKeys' ] );
			$settings->set_api_keys( $keys );
		}

		if ( ! empty( $saved[ 'enhancedDelivery' ] ) ) {
			$settings->set_enhanced_delivery( $saved[ 'enhancedDelivery' ] );
		}

		if( ! empty( $saved[ 'plan' ] ) ){
			$settings->set_plan( $saved[ 'plan' ] );
		}else{
			$settings->set_plan( 'basic' );
		}

		return $settings;
	}


	/**
	 * Add an individual form's settings
	 *
	 * @param form $form $form Form Settings object
	 *
	 * @return form
	 */
	public function set_form( $form ){

		if( ! $this->has( $form->get_form_id() ) ){
			$forms = $this->get( 'forms', array() );

			$forms[] = $form->get_form_id();
			$this->set( 'forms', $forms );
		}

		$this->set( $form->get_form_id(), $form );


		return $this->get( $form->get_form_id() );

	}

	/**
	 * Get form object by ID
	 *
	 * Will attempt to get from repo, then DB. Failing that will create empty instance
	 *
	 * @param string $form_id Form ID
	 *
	 * @return form
	 */
	public function get_form( $form_id ){
		if( ! $this->has( $form_id ) ){
			$_form = form::from_saved( $form_id );
			if( is_object( $_form ) ){
				$this->set( $form_id, $_form );
			}
		}

		return $this->get( $form_id, new form( $form_id ) );

	}

	/**
	 * Set CF Pro account ID
	 *
	 * @param int $id Account ID
	 *
	 * @return $this
	 */
	public function set_account_id( $id ){
		$this->account_id = absint( $id );
		return $this;
	}

	/**
	 * Get CF Pro account ID
	 *
	 * @return int
	 */
	public function get_account_id(){
		return absint( $this->account_id );
	}

	/**
	 * Set CF Pro public key
	 *
	 * @param string $public
	 *
	 * @return $this
	 */
	public function set_api_public( $public ){
		$keys = $this->get_api_keys();
		$keys->set_public( $public );
		return $this;
	}

	/**
	 * Set CF Pro secret key
	 *
	 * @param $secret
	 *
	 * @return $this
	 */
	public function set_api_secret( $secret ){
		$keys = $this->get_api_keys();
		$keys->set_secret( $secret );
		return $this;

	}

	/**
	 * Get saved api keys object
	 *
	 * @since 0.0.1
	 *
	 * @return keys
	 */
	public function get_api_keys(){
		if( ! $this->has( 'apiKeys' ) ){
			$this->set( 'apiKeys', new keys() );
		}

		return $this->get( 'apiKeys' );

	}

	/**
	 * Set API keys object
	 *
	 * @param keys $keys
	 */
	public function set_api_keys( $keys ){
		$this->set( 'apiKeys', $keys );
	}

	/**
	 * Set if enhanced delivery is enabled
	 *
	 * @param bool $enable
	 */
	public function set_enhanced_delivery( $enable ){
		$this->set( 'enhanced_delivery',  $enable  );
	}

	/**
	 * Check if enhanced delivery setting is enabled
	 *
	 * NOTE: Does not check if it is possible, which $this->send_local() and $this->send_remote() do.
	 *
	 * @return bool
	 */
	public function get_enhanced_delivery(){
		return $this->get( 'enhanced_delivery', true );

	}

	/**
	 * Checks if we should use local email system or not
	 *
	 * @return bool
	 */
	public function send_local(){
		return ! $this->send_remote();
	}

	/**
	 * Checks if we should send with remote API or not
	 *
	 * @return bool
	 */
	public function send_remote(){
		if ( 'apex' === $this->get_plan() ) {
			return $this->get_enhanced_delivery();
		}

		return false;

	}

	/**
	 * Set plan type
	 *
	 * @param string $plan
	 */
	public function set_plan( $plan ){
		if( in_array( $plan, [ 'basic', 'apex', 'awesome' ] ) ){
			$this->set( 'plan', $plan );
		}

	}

	/**
	 * Get plan type
	 *
	 * @return string
	 */
	public function get_plan(){
		return $this->get( 'plan', 'basic' );
	}

	/**
	 * Return if is basic plan
	 *
	 * @return bool
	 */
	public function is_basic(){
		return 'basic' === $this->get_plan();
	}

	/**
	 * Save settings to database
	 *
	 * @since 0.0.1
	 */
	public function save(){
		foreach ( $this->forms() as $form ){
			$form->save();
		}

		$data = $this->toArray();
		unset( $data[ 'forms' ] );
		update_option( $this->option_key, $data  );

	}




	/**
	 * Get settings as array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function toArray(){
		$data = array(
			'account_id'       => $this->get_account_id(),
			'apiKeys'          => $this->get_api_keys()->toArray(),
			'forms'            => $this->forms_to_array(),
			'enhancedDelivery' => $this->get_enhanced_delivery(),
			'plan'             => $this->get_plan(),
		);

		return $data;
	}

	/**
	 * Get all form settings as array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	protected function forms(){
		$forms = array();
		$all_forms = \Caldera_Forms_Forms::get_forms();
		foreach ( $all_forms as $form ){
			$forms[ ] = $this->get_form( $form );
		}
		return $forms;
	}

	/**
	 * Get all forms as an array
	 *
	 * @return array
	 */
	protected function forms_to_array(  ){
		$array = [];
		foreach ( $this->forms() as $form ){
			$array[] = $form->toArray();
		}
		return $array;
	}


}