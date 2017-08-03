<?php


namespace calderawp\calderaforms\pro\log;
use calderawp\calderaforms\pro\container;
use Monolog\Handler\AbstractHandler;


/**
 * Class handler - Monolog handler for CF Pro remote logging
 * @package calderawp\calderaforms\pro\log
 */
class handler extends  AbstractHandler {


	/**
	 * {@inheritdoc}
	 * @since 0.5.0
	 */
	public function handle( array $record)
	{
		$prepared = $this->prepare( $record );
		$level = isset( $record[ 'level_name' ] ) ? $record[ 'level_name' ] : 'NOTICE' ;
		$message = isset( $record[ 'message' ] ) ? $record[ 'message' ] : '' ;
		container::get_instance()->get_logger()->send(
			$message,
			$prepared,
			$level
		);

	}


	/**
	 * Prepare data to be sent to remote API
	 *
	 * @since 0.5.0
	 *
	 * @param array $record
	 *
	 * @return array
	 */
	protected function prepare( array $record ){
		$prepared = [];

		$prepared[ 'location' ] = [
			'file' => isset( $record[ 'context' ][ 'file' ] ) ?  $record[ 'context' ][ 'file' ] : '',
			'line' => isset( $record[ 'context' ][ 'line' ] ) ? $record[ 'context' ][ 'line' ] : '',

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
		if( isset( $record[ 'extra' ] ) ){
			$prepared = array_merge( $prepared, $record[ 'extra' ] );
		}

		if( method_exists( 'Caldera_Forms_DB_Tables', 'get_missing_tables' ) && is_object( container::get_instance()->get_tables() ) ){
			$prepared[ 'missing_tables' ] = container::get_instance()->get_tables()->get_missing_tables();
		}

		return $prepared;



	}

}