<?php


namespace calderawp\calderaforms\pro\api;


/**
 * Class log
 * @package calderawp\calderaforms\pro\api
 */
class log extends api{

	/**
	 * Send a log event
	 *
	 * @since 0.2.0
	 *
	 * @param string $message Log message
	 * @param array $data Optional log data
	 *
	 * @return array|\WP_Error
	 */
	public function send( $message, array  $data = [] )
	{
		global $wp_version;
		$data[ 'data' ] = $data;
		$data[ 'data' ][ 'versions' ] = [
			'php' => PHP_VERSION,
			'client' => CF_PRO_VER,
			'cf' => CFCORE_VER,
			'wp' => $wp_version
		];

		$data[ 'public' ] = $this->get_public();
		$data[ 'url' ] = home_url();
		$data[ 'message' ] = $message;
		return $this->request( '/log/client', $data );
	}

	/** @inheritdoc */
	protected function set_request_args( $method )
	{
		$args = array(
			'headers' => array(
				'X-CS-PUBLIC'  => $this->get_public(),
				'content-type' => 'application/json'

			),
			'method'  => $method
		);

		return $args;
	}

	/**
	 * Get public key
	 *
	 * @since 0.2.0
	 *
	 * @return int|string
	 */
	protected function get_public()
	{
		$public = $this->keys->get_public();
		if( ! empty( $public ) ){
			return $public;
		}

		return 0;

	}
}