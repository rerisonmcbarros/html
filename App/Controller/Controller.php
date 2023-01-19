<?php

namespace App\Controller;

use \Lib\Core\Request;
use \Lib\Core\Session;
use \Lib\Database\Record;
use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Core\Uploader;
use \Lib\Core\Message;
use \App\Model\Category;
use \App\Model\Product;
use \App\Model\ImageProduct;
use \App\View\Engine;

class Controller{
	
	protected $request;
	protected $session;
	protected $message;

	public function __construct(Request $request){

		$this->message = new Message();
		$this->session = new Session();
		$this->request = $request;
	}
}