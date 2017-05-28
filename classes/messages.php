<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\exceptions\Exception;
use calderawp\calderaforms\pro\exceptions\factory;


/**
 * Class messages
 *
 * CRUD for local DB records of CF Pro messages
 *
 * @package calderawp\calderaforms\pro
 */
class messages {

	/***
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * The name of table we are using to store
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * messages constructor.
	 *
	 * @param \wpdb $wpdb WPDB object
	 * @param string $table_name The name of table we are using to store -- SHOULD BE PREFIXED

	 */
	public function __construct( \wpdb $wpdb, $table_name  ){
		$this->wpdb = $wpdb;
		$this->table_name = $table_name;
	}


	/**
	 * Store message record
	 *
	 * @since 0.0.1
	 *
	 * @param int $cfp_id Message ID from app
	 * @param string $hash Message hash
	 * @param int $entry_id Entry ID
	 *
	 * @return message
	 */
	public function create( $cfp_id, $hash, $entry_id = 0 ){
		$data = array(
			'cfp_id' => $cfp_id,
			'hash' => $hash,
			'entry_id' => $entry_id
		);
		$this->wpdb->insert( $this->table_name, $data, array(
				'%d',
				'%s',
				'%d'
			)
		);

		if( is_numeric( $this->wpdb->insert_id ) ){
			$data[ 'local_id' ] = $this->wpdb->insert_id;
			return message::from_array( $data );
		}

		throw New Exception( __( 'Could not store message', 'caldera-forms' ) );

	}




	/**
	 * Find by CF Pro app message ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Message ID from app
	 *
	 * @return message
	 */
	public function get_by_remote_id( $id ){
		try{
			$message = $this->get_by( 'cfp_id', $id );
			return $message;
		}catch ( Exception $e ){

		}
	}

	/**
	 * Find by local (database table) ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Message ID in local db
	 *
	 * @return message
	 */
	public function get_by_local_id( $id ){
		try{
			$message = $this->get_by( 'ID', $id );
			return $message;
		}catch ( Exception $e ){

		}
	}

	/**
	 * Find by entry ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id
 	 *
	 * @return message
	 */
	public function get_by_entry_id( $id ){
		try{
			$message = $this->get_by( 'entry_id', $id );
			return $message;
		}catch ( Exception $e ){

		}
	}


	/**
	 *
	 * @since 0.0.1
	 *
	 * @param string $field field to search by
	 * @param int $value Value to search for cfp_id|ID|entry_id
	 *
	 * @return message
	 * @throws Exception
	 */
	protected function get_by( $field, $value ){
		$table = $this->table_name;
		$results = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM $table WHERE `%s` = %d", $field, absint( $value ) ), ARRAY_A );
		if( $results ){
			return message::from_array( $results );
		}

		throw new Exception( __( 'Could not find message.', 'caldera-forms' ) );

	}

	/**
	 * Delete a saved entry
	 *
	 * @since 0.0.1
	 *
	 * @param string $field field to search by
	 * @param int $value Value to search for cfp_id|ID|entry_id
	 *
	 * @return false|int
	 */
	public function delete( $field, $value ){
		return $this->wpdb->delete( $this->table_name, array(
			$field => $value
		));

	}

}