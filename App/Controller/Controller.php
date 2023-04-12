<?php

namespace App\Controller;

use Lib\Core\Request;
use Lib\Core\Session;
use Lib\Utilities\Message;

class Controller
{	
	protected $request;
	protected $session;
	protected $message;

	public function __construct(Request $request)
	{
		$this->message = new Message();
		$this->session = new Session();
		$this->request = $request;
	}
}
