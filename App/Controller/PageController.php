<?php

namespace App\Controller;

use \Lib\Core\Request;
use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Core\Message;
use \App\View\Engine;
use \App\Controller\Controller;

class PageController extends Controller{
	

	public function home(){
	
		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$message = $this->message->success("olá mundo!");

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $e->getMessage();
		}
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("home", [
		'message' => ($message ?? '')	
		]);	
	}

}