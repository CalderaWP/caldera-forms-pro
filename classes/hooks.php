<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\admin\menu;
use calderawp\calderaforms\pro\admin\scripts;
use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\api\local\settings;
use calderawp\calderaforms\pro\api\message;
use calderawp\calderaforms\pro\settings\active;


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
	 *
	 * @since 0.0.1
	 */
	public function add_hooks(){
		add_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );

		if( active::get_status() ){
			add_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 99, 4 );
			add_filter( 'caldera_forms_ajax_return', array( $this, 'add_pdf_link_ajax' ), 10, 2 );
			add_filter( 'caldera_forms_render_notices', array( $this, 'add_pdf_link_not_ajax' ), 10, 2 );
			add_filter( 'caldera_forms_autoresponse_mail', array( $this, 'auto_responder' ), 99, 4 );
			add_action( 'caldera_forms_pro_loaded', array( $this, 'init_logger' ) );
			add_action( 'caldera_forms_checked_tables', array( $this, 'capture_tables_object' ) );

		}


		add_action( pdf::CRON_ACTION, array( $this, 'delete_file' ) );

	}

	/**
	 * Remove hooks needed for CF Pro
	 *
	 * @since 0.0.1
	 */
	public function remove_hooks(){
		remove_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 10 );
		remove_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );
		remove_filter( 'caldera_forms_ajax_return', array( $this, 'add_pdf_link_ajax' ), 10 );
		remove_filter( 'caldera_forms_render_notices', array( $this, 'add_pdf_link_not_ajax' ), 10);
		remove_filter( 'caldera_forms_autoresponse_mail', array( $this, 'auto_responder' ), 99 );
		remove_action( pdf::CRON_ACTION, array( $this, 'delete_file' ) );

	}

	/**
	 * Intercept emails and send to remote app if called for
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @sine 0.0.1
	 *
	 * @param $mail
	 * @param $data
	 * @param $form
	 * @param $entry_id
	 *
	 * @return null|array
	 */
	public function mailer( $mail, $data, $form, $entry_id ){
		$form_settings = container::get_instance()->get_settings()->get_form( $form[ 'ID' ] );
		if ( ! $form_settings ) {
			return $mail;
		}

		$send_local = $form_settings->should_send_local();
		$send_remote = ! $send_local;
		$message = send::main_mailer( $mail, $entry_id, $form [ 'ID' ], $send_remote );
		if ( is_object( $message ) && ! is_wp_error( $message ) ) {
			if ( $send_local &&  $form_settings->should_attatch_pdf() ) {
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

	/**
	 * Handle autoresponder emails
	 *
	 * @sine 0.0.1
	 *
	 * @uses "caldera_forms_autoresponse_mail" filter
	 *
	 * @param $mail
	 * @param $config
	 * @param $form
	 * @param $entry_id
	 *
	 * @return null
	 */
	public function auto_responder( $mail, $config, $form, $entry_id ){

		$form_settings = container::get_instance()->get_settings()->get_form( $form[ 'ID' ] );
		if ( ! $form_settings ) {
			return $mail;
		}
		$send_local = $form_settings->should_send_local();
		$send_remote = ! $send_local;


		$message = new \calderawp\calderaforms\pro\api\message();
		$message->add_recipient( 'reply',
			\Caldera_Forms::do_magic_tags( $config[ 'sender_email' ] ),
			\Caldera_Forms::do_magic_tags($config[ 'sender_name' ] )
		);

		$message->add_recipient( 'to',
			\Caldera_Forms::do_magic_tags( $config[ 'recipient_email' ] ),
			\Caldera_Forms::do_magic_tags( $config[ 'recipient_name'] )
		);

		$message->subject = $mail ['subject' ];
		$message->content = $mail[ 'message' ];
		$message->pdf_layout = $form_settings->get_pdf_layout();
		$message->layout = $form_settings->get_layout();
		$message->add_entry_data( $entry_id, $form );
		$message->entry_id = $entry_id;
		$sent = send::send_via_api( $message, $entry_id, $send_remote, 'auto' );
		if( is_object( $sent ) && ! is_wp_error( $sent ) ){
			if( $send_local ){
				return $mail;
			}else{
				return null;
			}
		}

		return $mail;
	}

	/**
	 * Add the PDF link to AJAX response
	 *
	 * @since 0.1.0
	 *
	 * @uses "caldera_forms_ajax_return" filter
	 *
	 * @param array $out Response data
	 * @param array $form Form config
	 *
	 * @return mixed
	 */
	public function add_pdf_link_ajax( $out, $form ){
		if( isset( $data[ 'cf_er' ] )  ){
			return $out;
		}

		$settings = container::get_instance()->get_settings()->get_form( $form['ID'] );
		if( ! $settings ||!  $settings->should_add_pdf_link() ){
			return $out;
		}

		$entry_id = $out[ 'data' ][ 'cf_id' ];
		$message = container::get_instance()->get_messages_db()->get_by_entry_id( $entry_id );
		if( $message && ! is_wp_error( $message ) ) {
			$link = $message->get_pdf_link();
			if( filter_var( $link, FILTER_VALIDATE_URL ) ) {
				$out[ 'html' ] .= caldera_forms_pro_link_html( $form, $link );
			}

		}

		return $out;

	}

	/**
	 * Add link to success messages when the form is NOT submitted via AJAX
	 *
	 * @since 0.0.1
	 *
	 * @uses "caldera_forms_render_notices" filter
	 *
	 * @param array $notices
	 * @param $form
	 *
	 * @return array
	 */
	public function add_pdf_link_not_ajax( $notices, $form ){
		if ( ! isset( $_GET[ 'cf_id' ] ) || ! isset( $_GET[ 'cf_su' ] ) ) {
			return $notices;
		}
		$entry_id = absint( $_GET[ 'cf_id' ] );
		$message = container::get_instance()->get_messages_db()->get_by_entry_id( $entry_id );
		if( $message && ! is_wp_error( $message ) ) {
			$link = $message->get_pdf_link();
			if( filter_var( $link, FILTER_VALIDATE_URL ) ){

				$html = caldera_forms_pro_link_html( $form, $link );
				if( isset( $notices[ 'success' ], $notices[ 'success' ][ 'note' ] ) ){
					$notices[ 'success' ][ 'note' ] = '<div class="cf-pro-pdf-link alert alert-success">' . $notices[ 'success' ][ 'note' ] . '</div>' . $html;
				}else{
					$notices[ 'success' ][ 'note' ] = $html;
				}

			}
		}

		return $notices;

	}


	/**
	 * Sets up CF REST API endpoint for the settings
	 *
	 * @since 0.0.1
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
	 * @since 0.0.1
	 *
	 * Uses cron action set in pdf::CRON_ACTION
	 *
	 * @param string $file Absolute path to file to be deleted
	 */
	public function delete_file( $file ){
		unlink( $file );
	}

	/**
	 * Initialize remove logger
	 *
	 * @since 0.5.0
	 *
	 * @uses "
	 */
	public function init_logger(){
		/**
		 * Filter to disable Caldera Forms Pro log mode
		 *
		 * @since 0.6.0
		 *
		 * @param bool $param $use
		 */
		$use = apply_filters( 'caldera_forms_pro_log_mode', true );
		if( ! $use ){
			return;
		}
		if( ! is_object( container::get_instance()->get_tables() ) ){
			\Caldera_Forms::check_tables();
		}
		\Inpsyde\Wonolog\bootstrap( new \calderawp\calderaforms\pro\log\handler() );
		add_action( 'wp_footer', [ $this, 'log_js' ] );
		add_action( 'caldera_forms_edit_end', [ $this, 'log_js' ] );
		add_action( 'caldera_forms_admin_footer', [ $this, 'log_js' ] );
	}

	/**
	 * Caputure Caldera_Forms_DB_Tables object for reuse later
	 *
	 * @since 0.5.0
	 *
	 * @uses "caldera_forms_checked_tables"
	 *
	 * @param  \Caldera_Forms_DB_Tables $tables
	 */
	public function capture_tables_object( $tables ){
		container::get_instance()->set_tables( $tables );
	}

	public function log_js(){
		$keys = container::get_instance()->get_settings()->get_api_keys();

		$public = $keys->get_public();
		if( empty( $public ) ){
			return;
		}
		$token = $keys->get_token();
		$url = caldera_forms_pro_log_url();
		if( ! is_ssl() ){
			$url = str_replace( 'https', 'http', $url );
		}
		$url .= '/log';

		?>
		<script>
			window.onerror = function(message, url, lineNumber) {
				jQuery.post({
					crossOrigin: true,
					url: "<?php echo ( $url ); ?>",
					data: {
						message: message,
						data: {
							line: lineNumber,
							url: url
						}
					},
					beforeSend: function (xhr) {
						xhr.setRequestHeader( 'X-CS-PUBLIC', '<?php echo $public; ?>' );
						xhr.setRequestHeader( 'X-CS-TOKEN', '<?php echo $token; ?>' );
					}
				} );
				return true;
			};
		</script>
		<?php
	}
}