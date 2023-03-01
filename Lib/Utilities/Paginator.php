<?php

namespace Lib\Utilities;


class Paginator{

	private const STYLE_LINKS = 'page-link';
	private $url;
	private $limit;
	private $offset;
	private $currentPage;

	private $numberPages;
	private $numberLinks;

	private $totalResults;

	public function __construct($totalResults, $limit){

		$this->totalResults = $totalResults ?? 0;
		$this->url = $this->setUrl();
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

	private function getCurrentPage(){

		return filter_input(INPUT_GET, "page", FILTER_DEFAULT) ?? 1;
	}

	private function setNumberPages(){

		if($this->totalResults <= $this->limit){

			$this->numberPages = 1;
		}

		$this->numberPages = ceil($this->totalResults/$this->limit);
	}

	public function setNumberLinks($number){

		$this->numberLinks = $number;
	}

	public function links(){

		$linksAround = floor($this->numberLinks/2);

		$start = $this->currentPage-$linksAround;
		$end = $this->currentPage+$linksAround;

		if($start <= 1){

			$start = 1;	
			$end = $start + ($linksAround*2);
		}

		if($end >= $this->numberPages){

			$start = $this->numberPages - ($linksAround*2);
			$end = $this->numberPages;
		}

		$template = '';

		$template.= "<a class=\"".self::STYLE_LINKS."\" href=\"{$this->url}{$this->getPreviousPage()}\"><<</a>";

		for ($i=$start; $i <= $end; $i++) { 
	
			if($i == $this->currentPage){

				$template.= "<span class=\"".self::STYLE_LINKS." active\" >{$i}</span>";
			}
			else{

				if($i >= 1 && $i <= $this->numberPages){

					$template.= "<a class=\"".self::STYLE_LINKS."\" href=\"{$this->url}{$i}\">{$i}</a>";
				}
			}

		}

		$template.= "<a class=\"".self::STYLE_LINKS."\" href=\"{$this->url}{$this->getNextPage()}\">>></a>";

		return $template;
	}

	private function getNextPage(){

		if($this->currentPage >= $this->numberPages){

			return $this->currentPage;
		}

		return $this->currentPage+1;
	}

	private function getPreviousPage(){

		if($this->currentPage <= 1){

			return $this->currentPage;
		}

		return $this->currentPage-1;
	}

	private function setUrl(){

		$url = filter_input(INPUT_SERVER, "REQUEST_URI");

		if(strpos($url,'?') === false){

			$url= $url."?page=";
		}
		else{

			if(strstr($url,'&page=') !== false ){

				$url = strstr($url,'&page=', true);

				$url = $url."&page=";
			}
			elseif(strstr($url,'?page=') !== false ){

				$url = strstr($url,'?page=', true);

				$url = $url."?page=";
			}
			else{

				$url= $url."&page=";
			}	
		}

		return $url;
	}
}