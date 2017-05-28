<?php


namespace calderawp\calderaforms\pro\api;
use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\exceptions\Exception;


/**
 * Class client
 *
 * CF Pro API Client
 *
 * Both message abstractions decorate this object BTW
 *
 * @package calderawp\calderaforms\pro
 */
class client {


	/**
	 * API keys
	 *
	 * @var keys
	 */
	protected  $keys;

	/**
	 * client constructor.
	 *
	 * @param keys $keys API keys
	 */
	public function __construct( keys $keys )
	{
		$this->keys = $keys;
	}


	/**
	 * Create message on CF Pro
	 *
	 * @param \calderawp\calderaforms\pro\api\message $message Message data
	 * @param bool $send Should message be sent immediately?
	 *
	 * @return \calderawp\calderaforms\pro\message|\WP_Error|null
	 */
	public function create_message( $message, $send, $entry_id  ){
		$data = $message->to_array();
		if( $send ){
			$data[ 'send' ] = true;
		}

		$response = $this->request( '/message', $data, 'POST' );
		if( ! is_wp_error( $response ) && 201 == wp_remote_retrieve_response_code( $response ) ){
			$body = (array) json_decode( wp_remote_retrieve_body( $response ) );
			if( isset( $body[ 'hash' ] ) && isset( $body[ 'id' ] ) ){
				try {
					$saved_message = container::get_instance()->get_messages_db()->create( $body[ 'id' ], $body[ 'hash' ], $entry_id );
					return $saved_message;
				} catch ( Exception $e ) {
					return new \WP_Error( $e->getCode(), $e->getMessage() );
				}

			}
		}elseif ( is_wp_error( $response ) ){
			return $response;
		}

		return null;

	}

	/**
	 * Send previously saved CF Pro message
	 *
	 * @param int $message_id CF Pro ID of message
	 *
	 * @return array|\WP_Error
	 */
	public function send_saved( $message_id ){
		return $this->request( '/message/' . $message_id, array(), 'POST' );

	}

	/**
	 * Get PDF of previously saved message
	 *
	 * @param string $hash Hash of message
	 *
	 * @return array|\WP_Error
	 */
	public function get_pdf( $hash ){
		return $this->request( '/pdf/'. $hash, array() );

	}

	/**
	 * Make remote request
	 *
	 * @param string $endpoint Endpoint to use
	 * @param array $data Request data to be sent as body or query string for GET.
	 * @param string $method Optional. HTTP request method. Default is "GET"
	 *
	 * @return array|\WP_Error
	 */
	protected function request( $endpoint, $data, $method = 'GET' ){
		$url = caldera_forms_pro_app_url()  . $endpoint;
		$args = array(
			'headers' => array(
				'X-CS-TOKEN' => $this->keys->get_token(),
				'X-CS-PUBLIC' => $this->keys->get_public(),
				'content-type' => 'application/json'

			),
			'method' => $method
		);

		if( 'GET' == $method ){
			$url = add_query_arg( $data, $url );
		}else{
			$args[ 'body' ] = wp_json_encode( $data );
		}

		$request = wp_remote_request( $url, $args );
		return $request;

	}

}