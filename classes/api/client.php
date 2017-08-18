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
class client extends api {




	/**
	 * Create message on CF Pro
	 *
	 * @since 0.0.1
	 *
	 * @param \calderawp\calderaforms\pro\api\message $message Message data
	 * @param bool $send Should message be sent immediately?
	 * @param int $entry_id Local entry ID
	 * @param string $type Optional. The message type. Default is "main" Options: main|auto
	 * @return \calderawp\calderaforms\pro\message|\WP_Error|null
	 */
	public function create_message( $message, $send, $entry_id, $type = 'main'  ){
		$data = $message->to_array();
		if( $send ){
			$data[ 'send' ] = true;
		}

		$response = $this->request( '/message', $data, 'POST' );
		if( ! is_wp_error( $response ) && 201 == wp_remote_retrieve_response_code( $response ) ){
			$body = (array) json_decode( wp_remote_retrieve_body( $response ) );
			if( isset( $body[ 'hash' ] ) && isset( $body[ 'id' ] ) ){
				try {
					$saved_message = container::get_instance()->get_messages_db()->create( $body[ 'id' ], $body[ 'hash' ], $entry_id, $type );
					return $saved_message;
				} catch ( Exception $e ) {
					return $e->log( [
						'type' => $type,
						'entry_id' => $entry_id,
						'send' => $send,
						'method' => __METHOD__,
						'pdf' => $message->pdf
					] )->to_wp_error();
				}

			}
		}elseif ( is_wp_error( $response ) ){
			return $response;
		}

		return null;

	}

	/**
	 * Get API keys
	 *
	 * @since 0.1.1
	 *
	 * @return keys
	 */
	public function get_keys(){
		return $this->keys;
	}

	/**
	 * Send previously saved CF Pro message
	 *
	 * @since 0.0.1
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
	 * @since 0.0.1
	 *
	 * @param string $hash Hash of message
	 *
	 * @return array|\WP_Error
	 */
	public function get_pdf( $hash ){
		return $this->request( '/pdf/'. $hash, array() );

	}

	/**
	 * Get HTML of previously saved message
	 *
	 * @since 0.0.1
	 *
	 * @param int $message_id Message ID (CF Pro ID)
	 *
	 * @return string
	 */
	public function get_html( $message_id ){
		$r = $this->request( '/message/view/' . $message_id, array(), 'GET' );
		if( ! is_wp_error( $r ) && 200 == wp_remote_retrieve_response_code( $r ) ){
			return wp_remote_retrieve_body( $r );
		}

	}

	/**
	 * @inheritdoc
	 */
	protected function get_url_root(){
		return caldera_forms_pro_app_url();
	}

}