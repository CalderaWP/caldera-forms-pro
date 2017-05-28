<?php


namespace calderawp\calderaforms\pro\api;
use calderawp\calderaforms\pro\interfaces\arrayable;


/**
 * Class keys
 * @package calderawp\calderaforms\pro
 */
class keys implements arrayable {

	/**
	 * API secret key
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $secret;

	/**
	 * API public key
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $public;


	/**
	 * keys constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param string|null $public Optional. Public key
	 * @param string|null $secret Optional. Secret Key
	 */
	public function __construct( $public = null, $secret = null ){
		$this->set_public( $public );
		$this->set_secret( $secret );

	}

	/**
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function toArray(){
		return array(
			'token' => $this->get_token(),
			'public' => $this->public,
			'secret' => $this->secret,
		);

	}

	/**
	 * @param array $data
	 *
	 * @return keys
	 */
	public static function fromArray( array  $data = [] ){
		$keys = new keys();
		if( ! empty( $data[ 'public' ] ) ){
			$keys->set_public( $data[ 'public' ] );
		}
		if( ! empty( $data[ 'secret' ] ) ){
			$keys->set_secret( $data[ 'secret' ] );
		}

		return $keys;
	}

	/**
	 * Get API token
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_token(){
		return sha1( $this->public . $this->secret );

	}

	/**
	 * Get API public key
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_public(){
		return $this->public;

	}

	/**
	 * Set API key
	 *
	 * @since 0.0.1
	 *
	 * @return keys
	 */
	public function set_public( $key ){
		$this->public = $key;
		return $this;

	}

	/**
	 * Set API secret
	 *
	 * @since 0.0.1
	 *
	 * @return keys
	 */
	public function set_secret( $key ){
		$this->secret = $key;
		return $this;

	}


}