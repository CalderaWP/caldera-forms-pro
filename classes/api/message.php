<?php


namespace calderawp\calderaforms\pro\api;
use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\repository;


/**
 * Class message
 *
 * Representation of message in format API client likes
 *
 * @package calderawp\calderaforms\pro\api
 */
class message extends repository {

	/**
	 * API client
	 *
	 * @since 0.0.1
	 *
	 * @var client
	 */
	protected $client;

	/**
	 * Properties of messages
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $properites = [
		'layout',
		'pdf',
		'pdf_layout',
		'to',
		'reply',
		'cc',
		'bcc',
		'content',
		'subject'
	];

	/**
	 * Magic setter
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Property name
	 * @param mixed $value Value to set
	 */
	public function __set( $name, $value )
	{
		if( $this->allowed_key( $name )){
			$this->set( $name, $value );
		}
	}

	/**
	 * Create on remote API
	 *
	 * @since 0.0.1
	 *
	 * @param bool $send Send message now or delay?
	 * @param int $entry_id Local entry ID
	 *
	 * @return array|\WP_Error
	 */
	public function create( $send, $entry_id ){
		if( ! $this->client ){
			$this->client = new client( container::get_instance()->get_settings()->get_api_keys() );
		}


		return $this->client->create_message( $this, $send, $entry_id );

	}

	/**
	 * @inheritdoc
	 * @return message
	 */
	public function set( $key, $value ){
		if( $this->allowed_key( $key ) ){
			if( in_array( $key, array(
				'to',
				'reply',
				'cc',
				'bcc'
			)) ){
				if( is_string( $value ) ){
					$value = array(
						'email' => $value,
						'name'  => '',
					);
				}elseif ( is_array( $value ) && isset( $value[0] ) && is_email( $value[0] ) ){
					$value = array(
						'email' => $value[0],
						'name'  => '',
					);
				}

			}
			$this->items[ $key ] = $value;
		}

		return $this;

	}

	/**
	 * Can this key be set
	 *
	 * @since 0.0.1
	 *
	 * @param string $key Key to check
	 *
	 * @return bool
	 */
	protected function allowed_key( $key ){
		return in_array( $key, $this->properites );
	}

	/** @inheritdoc */
	public function to_array(){
		$array = array();
		foreach ( $this->properites as $prop  ){
			if ( $this->has( $prop ) ) {
				$array[ $prop ] = $this->get( $prop );
			}else{
				$array[ $prop ] = false;
			}
		}

		return $array;
	}

}