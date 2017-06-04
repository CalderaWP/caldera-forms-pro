<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\api\client;


/**
 * Class message
 *
 * Representation of messages in database
 *
 * Message in this context is local record of messages saved to CF Pro
 *
 * @package calderawp\calderaforms\pro
 */
class message extends json_arrayable {

	/**
	 * Database row ID
	 *
	 * @since 0.0.1
	 *
	 *@var int
	 */
	protected $local_id;

	/**
	 * CF Pro ID (ID on app)
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $cfp_id;

	/**
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * CF entry ID (local)
	 *
	 * @since 0.0.1
	 *
	 * @var int|null
	 */
	protected $entry_id;


	/**
	 * @var client
	 */
	protected $client;

	/**
	 * message constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param int $cfp_id
	 * @param string $hash
	 * @param int $local_id
	 * @param null|int $entry_id
	 */
	public function __construct( $cfp_id, $hash, $local_id, $entry_id = null )
	{
		$this->local_id = $local_id;
		$this->cfp_id   = $cfp_id;
		$this->hash     = $hash;
		$this->entry_id = $entry_id;
	}

	/**
	 * Convert to array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'entry_id' => $this->entry_id,
			'local_id' => $this->get_local_id(),
			'cfp_id'   => $this->get_cfp_id(),
			'hash'     => $this->get_hash()
		);
	}

	/**
	 * Factory to create object from array
	 *
	 * @param array $data
	 *
	 * @return message
	 */
	public static function from_array( array $data )
	{
		$obj = new self( $data[ 'cfp_id' ], $data[ 'hash' ], $data[ 'local_id' ] );
		if( isset( $data[ 'entry_id' ] ) ){
			$obj->entry_id = $data[ 'entry_id' ];
		}
		return $obj;
	}

	/**
	 * Magic method to allow CRUD to be called on this abstraction
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Method . Supported: save|delete
	 * @param $arguments
	 *
	 * @return message|false|int|null
	 */
	public function __call( $name, $arguments )
	{
		switch( $name ){
			case 'save' :
				if( empty( $this->local_id ) ){
					return container::get_instance()->get_messages_db()->create(
						$this->get_cfp_id(), $this->get_hash(), $this->get_entry_id()
					);
				}
				break;
			case 'delete' :
				if( empty( $this->local_id ) ){
					return container::get_instance()->get_messages_db()->delete(
						'ID', $this->get_local_id()
					);
				}
				break;
		}

		return null;

	}

	/** @inheritdoc */
	public function __set( $name, $value )
	{
		if( property_exists( $this, $name ) ){
			$this->$name = $value;
		}

	}

	/**
	 * Get local ID
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_local_id()
	{
		return $this->local_id;
	}

	/**
	 * Get Caldera Forms Pro ID (ID on remote app)
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_cfp_id()
	{
		return $this->cfp_id;
	}

	/**
	 * Get entry ID
	 *
	 * @since 0.0.1
	 *
	 * @return int|null
	 */
	public function get_entry_id(){
		return $this->entry_id;
	}

	/**
	 * Get message hash
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_hash()
	{
		return $this->hash;
	}

	/**
	 * Send previously saved message
	 *
	 * @return array|\WP_Error
	 */
	public function send(){
		return $this->get_client()->send_saved( $this->get_cfp_id() );
	}

	/**
	 * Get PDF of previously saved message
	 *
	 * @return array|\WP_Error
	 */
	public function get_pdf(){
		$r = wp_remote_get( caldera_forms_pro_app_url() . '/pdf/' . $this->get_hash() );
		if( ! is_wp_error( $r ) && in_array( intval( wp_remote_retrieve_response_code( $r ) ), array( 200, 201 ) ) ){
			return wp_remote_retrieve_body( $r );
		}
		if( is_wp_error( $r )  ){
			return $r;
		}

		return new \WP_Error();
	}

	public function get_html(){
		return $this->get_client()->get_html( $this->get_cfp_id() );
	}
	/**
	 * @return client
	 */
	protected function get_client(){

		if( ! $this->client ){
			$this->client = new client( container::get_instance()->get_settings()->get_api_keys() );
		}


		return $this->client;


	}

}