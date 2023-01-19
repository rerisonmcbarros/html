<?php

namespace Lib\Core;

class Session{
	

	public function __construct(){

		if(empty(session_id())){

			session_start();
		}
	}

	public function __get($name){

		if(isset($_SESSION[$name])){

			return $_SESSION[$name];
		}
	}
	public function __isset($name){

		return isset($_SESSION[$name]);
	}

	public function add($name, $value ){

		$_SESSION[$name] = $value;
	}

	public function all(){

		return (object)$_SESSION;
	}

	public function unset($name){

		if(isset($_SESSION[$name])){

			unset($_SESSION[$name]);
		}
	}

	public function destroy(){

		if(!empty(session_id())){

			session_unset();
			session_destroy();
		}
	}

}
