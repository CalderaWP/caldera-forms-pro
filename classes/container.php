<?php


namespace calderawp\calderaforms\pro;


/**
 * Class container
 *
 * Contains "main" instances of all reusable classes
 *
 * @package calderawp\calderaforms\pro
 */
class container extends repository{


	/**
	 * Holds main instance
	 *
	 * @var container
	 */
	protected static $instance;
	

	/**
	 * @return container
	 */
	public static function get_instance(){
		if( ! self::$instance ){
			self::$instance = new self();

		}

		return self::$instance;
	}

	/**
	 * Get the messages DB abstraction
	 *
	 * @return messages
	 */
	public function get_messages_db(){
		if( ! $this->has( 'db' ) ){
			global  $wpdb;
			$table_name = $wpdb->prefix . 'cf_pro_messages';
			$this->set( 'db',  new messages( $wpdb, $table_name ) );
		}
		return $this->get( 'db' );
	}

	/**
	 * Get the main settings object
	 *
	 * @return settings
	 */
	public function get_settings(){
		if( ! $this->has( 'settings' ) ){
			$this->set( 'settings', settings::from_saved() );
		}
		return $this->get( 'settings' );
	}

	/**
	 * Get hooks class
	 *
	 * @return hooks
	 */
	public function get_hooks(){
		if( ! $this->get( 'hooks' ) ){
			$this->set( 'hooks', new hooks() );
		}

		return $this->get( 'hooks' );
	}
}