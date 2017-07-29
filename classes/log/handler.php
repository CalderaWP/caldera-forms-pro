<?php


namespace calderawp\calderaforms\pro\log;
use calderawp\calderaforms\pro\container;
use Monolog\Handler\AbstractHandler;


/**
 * Class handler
 * @package calderawp\calderaforms\pro\log
 */
class handler extends  AbstractHandler {


	/**
	 * {@inheritdoc}
	 */
	public function handle( array $record)
	{
		container::get_instance()->get_logger()->send(
			$record[ 'message' ],
			$this->prepare( $record )
		);
	}

	protected function prepare( array $record){
		$prepared = [];
		$prepared[ 'location' ] = [
			'file' => isset( $record[ 'file' ] ) ? $record[ 'file' ] : '',
			'line' => isset( $record[ 'line' ] ) ? $record[ 'line' ] : '',

		];

		foreach ( array(
			'array',
			'property',
			'cb',
		) as $thing ){
			if( isset( $record[ 'context' ][ $thing ] ) ){
				$prepared[ $thing ] =  $record[ 'context' ][ $thing ] ;
			}


		}

		return $prepared;



	}

}