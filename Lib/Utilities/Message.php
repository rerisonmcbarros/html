<?php

namespace Lib\Utilities;

class Message
{
	private $message;
	private $type;

	public function __toString()
	{
		return $this->dump();
	}

	public function success($message)
	{
		$this->type = "alert alert-success";
		$this->message = $message;

		return $this;
	}

	public function error($message)
	{
		$this->type = "alert alert-danger";
		$this->message = $message;

		return $this;
	}

	public function warning($message)
	{
		$this->type = "alert alert-warning";
		$this->message = $message;

		return $this;
	} 

	public function info($message)
	{
		$this->type = "alert alert-primary ";
		$this->message = $message;

		return $this;
	}

	private function dump()
	{
		return "<div class='{$this->type}' role='alert'>{$this->message}</div>";
	}
}
