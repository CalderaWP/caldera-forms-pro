<?php


namespace calderawp\calderaforms\pro\exceptions;


/**
 * Class Exception
 * @package calderawp\calderaforms\pro\exceptions
 */
class Exception extends \Exception {


	/**
	 * Convert to WP_Error object
	 *
	 * @since 0.0.1
	 *
	 * @param array $data
	 *
	 * @return \WP_Error
	 */
	public function to_wp_error(  array $data = [] ){
		$wp_error = new \WP_Error( $this->getCode(), $this->getMessage(), $data );
		return $wp_error;

	}

}