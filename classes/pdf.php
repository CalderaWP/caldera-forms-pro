<?php


namespace calderawp\calderaforms\pro;


/**
 * Class pdf
 * @package calderawp\calderaforms\pro
 */
class pdf {

	/**
	 * CRON action to schedule deletes on
	 *
	 * @var string
	 */
	const  CRON_ACTION = 'cf_pro_delete_file';

	/**
	 * Path to local file
	 *
	 * @var string
	 */
	protected  $file;

	/**
	 * The message object
	 *
	 * @var message
	 */
	protected  $message;

	/**
	 * pdf constructor.
	 *
	 * @param message $message
	 */
	public function __construct( message $message )
	{
		$this->message = $message;
	}

	/**
	 * Uploadd locally
	 *
	 * @return string|null File path if uploaded. Null if not
	 */
	public function upload(){
		$pdf = pdf::get_by_hash( $this->message->get_hash() );
		if (  $pdf ) {
			$pdf_file = wp_upload_bits( uniqid() . '.pdf', null, $pdf );
			if ( isset( $pdf_file[ 'file' ] ) && false == $pdf_file[ 'error' ] && file_exists( $pdf_file[ 'file' ] ) ) {
				$mail[ 'attachments' ][] = $pdf_file[ 'file' ];
				$this->file              = $pdf_file[ 'file' ];

				return $pdf_file[ 'file' ];
			}
		}

		return null;

	}

	/**
	 * Delete local file
	 */
	public function delete_file(){
		unlink( $this->file );
	}

	/**
	 * Get PDF from app by hash
	 *
	 * @param $hash
	 *
	 * @return string
	 */
	public static function get_by_hash( $hash ){
		$r = wp_remote_get( caldera_forms_pro_app_url() . '/pdf/' . $hash );
		if( ! is_wp_error( $r ) && in_array( intval( wp_remote_retrieve_response_code( $r ) ), array( 200, 201 ) ) ){
			return wp_remote_retrieve_body( $r );
		}

	}

}