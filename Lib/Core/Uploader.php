<?php

namespace Lib\Core;


class Uploader{
	

	private $data;
	private $path;

	private $allowedTypes;

	public function __construct($file, bool $multiple){

		if($multiple == true){

			$this->setMultiple($file);
		}
		else{

			$this->setSingle($file);
		}

	}

	public function setAllowedTypes(array $types){

		$this->allowedTypes = $types;
	}

	private function setMultiple($files){

		$file = [];

		if(!empty($files['name'][0])){

			foreach($files['name'] as $index => $name){

				$file['name'] = $name;
				$file['full_path'] = $files['full_path'][$index];
				$file['type'] = strtolower($files['type'][$index]);
				$file['tmp_name'] = $files['tmp_name'][$index];
				$file['error'] = $files['error'][$index];
				$file['size'] = $files['size'][$index];
				$extension = ".".pathinfo($file['name'], PATHINFO_EXTENSION) ?? "";
				$file['new_name'] = uniqid("", true).$extension;
				
				$this->fileVerify($file);

				$this->data[] = $file;
			}
		}
	}


	private function setSingle($file){

		if(!empty($file['name'])){

			$file['type'] = strtolower($file['type']);
			$extension = ".".pathinfo($file['name'], PATHINFO_EXTENSION) ?? "";
			$file['new_name'] = uniqid("", true).$extension;

			$this->fileVerify($file);

			$this->data[] = $file;
		}
	}

	private function fileVerify($file){

		if($file['error'] == 1){

			throw new \Exception("Erro! o arquivo excedeu o limite de 2MB");
		}

		if(!empty($this->allowedTypes)){

			if(!in_array($file['type'], $this->allowedTypes)){

				throw new \Exception("Tipo de arquivo não permitido!");
			}
		}
	}

	public function moveUploadedFile(){

		if(!empty($this->data)){

			foreach($this->data  as $file){

				move_uploaded_file($file['tmp_name'], $this->path.$file['new_name']);
			}
		}	
	}

	public function setPath(string $path){

		$this->path = $path;
	}

	public function files(){

		if(!empty($this->data)){

			return $this->data;
		}
	}

}