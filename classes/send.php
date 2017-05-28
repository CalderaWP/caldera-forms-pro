<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\exceptions\Exception;


/**
 * Class send
 *
 * Class to handle turning Caldera Forms' $mail array into message objects and send them,
 *
 * @package calderawp\calderaforms\pro
 */
class send {

	/**
	 * Send main mailer to CF Pro
	 *
	 * @param array $mail the array provided on "caldera_forms_mailer" filter
	 * @param int $entry_id The entry ID
	 * @param string $form_id The form ID
	 * @param bool $send Optional. If message should be sent. Default is true. If false, will be stored but not sent.
	 *
	 * @return \calderawp\calderaforms\pro\message|array
	 */
	public static function main_mailer( $mail, $entry_id, $form_id, $send = true ){
		$form_settings = container::get_instance()->get_settings()->get_form( $form_id );
		if ( ! $form_settings ) {
			return $mail;
		}

		$message = new \calderawp\calderaforms\pro\api\message();
		$message->reply = array(
			'email' => $mail[ 'from' ],
			'name'  => $mail[ 'from_name' ]
		);
		$message->to = $mail[ 'recipients' ];
		$message->subject = $mail[ 'subject' ];
		$message->content = $mail[ 'message' ];
		$message->pdf_layout = $form_settings->get_pdf_layout();
		$message->layout = $form_settings->get_layout();
		if ( ! empty( $mail[ 'cc' ] ) ) {
			$message->cc = $mail[ 'cc' ];
		}

		if( ! empty( $mail[ 'bcc' ] ) ){
			$message->bcc = $mail[ 'bcc' ];
		}

		$client = new client( container::get_instance()->get_settings()->get_api_keys() );
		$response = $client->create_message( $message, $send, $entry_id );

		return $response;

	}

	//@TODO impliment for auto-responders
	public static function auto_responder( $mail, $entry_id ){

	}

}