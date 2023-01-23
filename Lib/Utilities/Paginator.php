<?php

namespace Lib\Utilities;


class Paginator{

	private $url;
	private $limit;
	private $offset;
	private $currentPage;

	private $numberPages;
	private $numberLinks;

	private $totalResults;


	public function __construct($totalResults, $url, $limit){

		$this->totalResults = $totalResults;
		$this->url = $url;
		$this->limit = $limit;
		$this->currentPage = $this->getCurrentPage();
		$this->offset = ($this->getcurrentPage()*$this->limit)-$this->limit;
		$this->setNumberPages(); 
	}

	public function getLimit(){

		return $this->limit;
	}

	public function getOffset(){

		return $this->offset;
	}

	public function getCurrentPage(){

		return filter_input(INPUT_GET, "page", FILTER_DEFAULT) ?? 1;
	}

	public function setNumberPages(){

		if($this->totalResults <= $this->limit){

			$this->numberPages = 1;
		}

		$this->numberPages = ceil($this->totalResults/$this->limit);
	}

	public function setNumberLinks($number){

		$this->numberLinks = $number;
	}

	public function links(){

		$linksAround = floor($this->numberLinks);

		$start = $this->currentPage-$linksAround;
		$end = $this->currentPage+$linksAround;

		$template = '';

		$template.= "<a href=\"{$this->url}?page={$this->getPreviousPage()}\"><<</a>";

		for ($i=$start; $i <= $end; $i++) { 
	
			if($i == $this->currentPage){

				$template.= "<span>{$i}</span>";
			}
			else{

				if($i >= 1 && $i <= $this->numberPages){

					$template.= "<a href=\"{$this->url}?page={$i}\">{$i}</a>";
				}
			}

		}

		$template.= "<a href=\"{$this->url}?page={$this->getNextPage()}\">>></a>";

		return $template;
	}

	public function getNextPage(){

		if($this->currentPage == $this->numberPages){

			return $this->currentPage;
		}

		return $this->currentPage+1;
	}

	public function getPreviousPage(){

		if($this->currentPage == 1){

			return $this->currentPage;
		}

		return $this->currentPage-1;
	}
}