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
		$pdf = $this->message->get_pdf();
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



}