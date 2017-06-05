<?php


namespace calderawp\calderaforms\pro\settings;
use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\json_arrayable;
use calderawp\calderaforms\pro\settings;


/**
 * Class form
 *
 * Settings for an individual form
 *
 * @package calderawp\calderaforms\pro\settings
 */
class form extends json_arrayable {


	protected $properties = array(
		'attach_pdf',
		'pdf_link',
		'layout',
		'pdf_layout',
	);

	protected $attributes = array();

	/**
	 * @var settings
	 */
	protected $settings;

	/**
	 * Form ID for this setting
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * form constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param string $form_id
	 */
	public function __construct(  $form_id )
	{

		$this->form_id = $form_id;
	}

	/**
	 * Factory to create from saved settings in database
	 *
	 * @param $form_id
	 *
	 * @return form|null
	 */
	public static function from_saved( $form_id ){
		$obj = new form( $form_id );
		$saved = get_option( $obj->option_key() );

		$obj->form_id = $form_id;
		if( is_array( $saved ) ){
			foreach ( $saved as $prop => $value ){
				$obj->$prop = $value;
			}

			return $obj;
		}

		return null;

	}

	/**
	 * Magic setter for allowed properties
	 *
	 * @since 0.0.1
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return form
	 */
	public function __set( $name, $value ){
		if( array_key_exists( $name, array_flip( $this->properties )) ){
			$this->set_property( $name, $value );
		}

		return $this;
	}

	/**
	 * Save to database
	 *
	 * @since 0.0.1
	 */
	public function save(){
		update_option( $this->option_key(), $this->toArray() );

	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function toArray(){
		$array = array();
		foreach ( $this->properties as $property ){
			$cb = 'get_' . $property;
			if( method_exists( $this, $cb   ) ){
				$array[ $property ] = $this->$cb();
			}else{
				$array[ $property ] = $this->get_property( $property, 'int' );
			}
		}
		$array[ 'form_id' ] = $this->form_id;
		$array[ 'name' ] = \Caldera_Forms::get_form( $this->form_id )[ 'name' ];
		return $array;
	}

	/**
	 * Get an array of properties -- settings we can save for form
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_properties(){
		return $this->properties;
	}



	/**
	 * Get form ID for this setting
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_form_id(){
		return $this->form_id;
	}


	/**
	 * Getter for send_local setting
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function get_send_local(){
		return true;
	}

	/**
	 * Getter for attatch_pdf setting
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function get_attach_pdf(){
		return $this->get_property( 'attach_pdf'  );
	}

	/**
	 *
	 * @return bool
	 */
	public function get_pdf_layout(){
		return $this->get_property( 'pdf_layout', 'int'  );

	}

	/**
	 * Getter for layout setting
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_layout(){
		return $this->get_property( 'layout' , 'int' );

	}

	/**
	 * Getter for form name
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_name(){
		return \Caldera_Forms::get_form( $this->form_id )[ 'name' ];
	}

	/**
	 * @param $prop
	 *
	 * @return bool
	 */
	protected function get_property( $prop, $cast = 'bool' ){
		if ( ! isset( $this->attributes[ $prop ] ) ) {
			$this->attributes[ $prop ] = false;
		}

		switch( $cast ) {
			case 'int' :
				return intval( $this->attributes[ $prop ] );
				break;
			default:
				return boolval( $this->attributes[ $prop ] );
				break;
		}
	}

	/**
	 * @param string $prop Property to set
	 * @param mixed $value Value to set
	 * @return bool|form
	 */
	protected function set_property( $prop, $value ){

		if( in_array( $prop, [
			'layout',
			'pdf_layout',
		]) ){
			$value = absint( $value );
		}else{
			$value = rest_sanitize_boolean( $value );
		}
		$this->attributes[ $prop ] = $value;

		return $this;
	}



	/**
	 * Option key to save in
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	protected function option_key(){
		return '_cf_pro_' . $this->form_id;
	}

}