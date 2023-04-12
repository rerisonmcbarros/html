<?php

namespace Lib\Core;

class Request
{	
	private $httpMethod;
	private $uri;
	public $post;
	public $get;
	private $headers;
	private $prefix;

	public function __construct($prefix)
	{
		$this->httpMethod = filter_input(INPUT_SERVER,"REQUEST_METHOD", FILTER_DEFAULT);
		$this->post       = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
		$this->get        = filter_input_array(INPUT_GET, FILTER_DEFAULT) ?? [];
		$this->uri        = parse_url(filter_input(INPUT_SERVER,"REQUEST_URI", FILTER_DEFAULT), PHP_URL_PATH);
		$this->prefix     = $prefix;
		$this->headers    = getallheaders() ?? false;
	}

	public function httpMethod()
	{
		return $this->httpMethod;
	}

	public function get()
	{
		return $this->get;
	}

	public function post()
	{
		return $this->post;
	}

	public function uri()
	{
		$prefix = $this->prefix;
		$prefixLen = strlen($this->prefix);
		
		if (!strpos($this->uri, $prefix)) {

			$uri = $this->uri;
		}
		
		$uri = substr(strstr($this->uri, $this->prefix), $prefixLen);

		return $uri;
	}

	public function headers()
	{
		return $this->headers;
	}

	public function json()
	{
		return json_encode([
			'method'  => $this->httpMethod(),
			'uri'     => $this->uri(),
			'headers' => $this->headers(), 
			'get'     => $this->get(),
			'post'    => $this->post()
		]);
	}
}
