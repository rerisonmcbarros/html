<?php

namespace Lib\Utilities;


class Paginator{

	private $url;
	private $limit;
	private $offset;

	private $currentPage;


	public function __construct($url, $limit){

		$this->url = $url;
		$this->limit = $limit;
		$this->currentPage = $this->setCurrentPage();
		$this->offset = 
	}


	public function getLimit(){

		return $limit;
	}

	public function setCurrentPage(){

		$this->currentPage = filter_input_array(INPUT_GET, "page");
	}


}