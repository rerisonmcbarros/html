<?php

namespace Lib\Log;

abstract class Logger{
	
	protected $filePath;

	public function __construct(string $filePath){

		$this->filePath = $filePath;
	}
	
	abstract function write($message);

}