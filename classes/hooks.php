<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\api\local\settings;
use calderawp\calderaforms\pro\api\message;


/**
 * Class hooks
 *
 * Handles interaction with WordPress plugins API
 *
 * @package calderawp\calderaforms\pro
 */
class hooks {

	/**
	 * Add hooks needed for CF Pro
	 */
	public function add_hooks(){
		add_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 99, 4 );
		add_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );
		add_action( pdf::CRON_ACTION, array( $this, 'delete_file' ) );

	}

	/**
	 * Remove hooks needed for CF Pro
	 */
	public function remove_hooks(){
		remove_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 10 );
		remove_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );
	}

	/**
	 * Intercept emails and send to remote app if called for
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @param $mail
	 * @param $data
	 * @param $form
	 * @param $entry_id
	 *
	 * @return null|array
	 */
	public function mailer( $mail, $data, $form, $entry_id )
	{
		$form_settings = container::get_instance()->get_settings()->get_form( $form[ 'ID' ] );
		if ( ! $form_settings ) {
			return $mail;
		}

		$send_local = $form_settings->should_send_local();
		$send_remote = ! $send_local;
		$message = send::main_mailer( $mail, $entry_id, $form [ 'ID' ], $send_remote );
		if ( is_object( $message ) && ! is_wp_error( $message ) ) {
			if (  $form_settings->should_attatch_pdf() ) {
				$mail = send::attatch_pdf( $message, $mail );
			}
		}else{
			return $mail;

		}

		if ( $send_local ) {
			if (  $form_settings->use_html_layout() ) {
				$mail[ 'message' ] = $message->get_html();
			}

			return $mail;
		}

		return null;

	}

	//@TODO ADD PDF link if needed
	public function add_pdf_link_ajax(){}
	public function add_pdf_link_not_ajax(){}

	/**
	 * Sets up CF REST API endpoint for the settings
	 *
	 * @uses "caldera_forms_rest_api_pre_init" action
	 *
	 * @param \Caldera_Forms_API_Load api
	 */
	public function init_api( $api ){
		$api->add_route( new settings() );
	}

	/**
	 * Delete a file
	 *
	 * Uses cron action set in pdf::CRON_ACTION
	 *
	 * @param string $file Absolute path to file to be deleted
	 */
	public function delete_file( $file ){
		unlink( $file );
	}
}