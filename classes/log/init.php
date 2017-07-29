<?php


namespace calderawp\calderaforms\pro\log;

use Inpsyde\Wonolog;
use Monolog\Handler;
use Monolog\Logger;

/**
 * Class init
 * @package calderawp\calderaforms\pro\log
 */
class init {


	public static function bootstrap(){
		Wonolog\bootstrap(
			new \calderawp\calderaforms\pro\log\handler()
		);


		//add_filter( 'wonolog.default-handler-folder', [ __CLASS__, 'log_dir' ] );
	}

	public static function log_dir( $dir ){
		return $dir;
	}
}