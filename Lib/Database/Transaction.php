<?php

namespace Lib\Database;

use \Lib\Database\Connect;
use \Lib\Log\Logger;

class Transaction{
	
	private static $pdo;
	private static $logger;

	public static function open($db_config){

		if(empty(self::$pdo)){

			self::$pdo = Connect::start($db_config);

			self::$pdo->beginTransaction();

			return true;
		}

		return false;
	} 

	public static function rollBack(){

		if(!empty(self::$pdo)){

			self::$pdo->rollBack();

			self::$pdo = null;

			return true;
		}

		return false;
	}

	public static function close(){

		if(!empty(self::$pdo)){

			self::$pdo->commit();

			self::$pdo = null;

			return true;
		}

		return false;
	}

	public static function get(){

		if(!empty(self::$pdo)){

			return self::$pdo;
		}

		return null;
	} 

	public static function setLogger(Logger $logger){

		self::$logger = $logger;
	}

	public static function log($message){

		if(!empty(self::$logger)){

			$logger = self::$logger;
			$logger->write($message);
		}
	}
}
