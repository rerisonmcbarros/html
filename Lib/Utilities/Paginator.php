<?php

namespace Lib\Utilities;


class Paginator{

	private $url;
	private $limit;
	private $offset;
	private $currentPage;

	private $numberPages;
	private $numberLinks;


	public function __construct($totalResults, $url, $limit){

		$this->url = $url;
		$this->limit = $limit;
		$this->currentPage = $this->getCurrentPage();
		$this->offset = ($this->currentPage()*$this->limit)-$this->limit;
		$this->numberPages = ceil($totalResults/$this->limit);
	}

	public function getLimit(){

		return $this->limit;
	}

	public function getOffset(){

		return $this->offset;
	}

	public function getCurrentPage(){

		return filter_input_array(INPUT_GET, "page") ?? 1;
	}

	public function setNumberLinks($number){

		$this->numberLinks = $number;
	}

	public function links(){

		$linksAround = floor($this->numberLinks);

		$start = $this->currentPage-$linksAround;
		$end = $this->currentPage+$linksAround;

		$template = '';

		for ($i=$start; $i <= $end; $i++) { 
			
			echo $i;

			if($i == $this->currentPage){

				$template.= "<span>{$i}</span>";
			}
			else{

				if($i >= 1 && $i <= $this->numberPages){

					$template.= "<a href=\"{$this->url}?page={$i}\">{$i}</a>"
				}
			}

		}

		return $template;
	}
}