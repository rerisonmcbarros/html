<?php

namespace Lib\Database;

class Connect{
	
	private static $connection;

	private function __construct(){}
	private function __clone(){}

	public static function start(array $db_config){

		try{

			$drive    = !empty($db_config["DB_DRIVE"])  ? $db_config["DB_DRIVE"]  : null;
			$host     = !empty($db_config["DB_HOST"])   ? $db_config["DB_HOST"]   : null;
			$name     = !empty($db_config["DB_NAME"])   ? $db_config["DB_NAME"]   : null;
			$port     = !empty($db_config["DB_PORT"])   ? $db_config["DB_PORT"]   : null;
			$user 	  = !empty($db_config["DB_USER"])   ? $db_config["DB_USER"]   : null;
			$password = !empty($db_config["DB_PASSWD"]) ? $db_config["DB_PASSWD"] : null;

			switch ($drive){

				case 'mysql':

					self::$connection = new \PDO("mysql:host={$host};port={$port};dbname={$name}",
						$user,
						$password,
						[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
					);
					break;

				default:

					throw new \Exception("Database not Implemented!");
					break;
			}

			return self::$connection;

		}
		catch(\PDOException $e){

			return "{$e->getMessage()} - {$e->getFile()} - {$e->getLine()}";
		}

	}

}


