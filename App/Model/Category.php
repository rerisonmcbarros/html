<?php

namespace App\Model;

use Lib\Database\Record;
use Lib\Database\Transaction;

class Category extends Record{
	
	public const TABLE_NAME = 'categoria';

	public function setCodigo(string $codigo){

		if($codigo === null || $codigo === ''){

			throw new \Exception("O código da categoria não pode ser vazio!");
		}

		if(!is_numeric($codigo)){

			throw new \Exception("O código da categoria deve ser um valor numérico!");
		}

		$this->data['codigo'] = $codigo;
	}

	public function setNome(string $nome){

		if($nome === null || $nome === ''){

			throw new \Exception("O nome da categoria não pode estar vazio!");
		}

		if(is_numeric($nome) ){

			throw new \Exception("O nome da categoria não pode ser um valor numérico!");
		}

		$this->data['nome'] = $nome;
		
	}

	/** @param int $codigo */
	/** @param string $columns */
	/** @return object|string */ 
	public function findByCodigo($codigo, $columns = '*'){

		try{

			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM ".self::TABLE_NAME." WHERE codigo = :codigo";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":codigo", $codigo, \PDO::PARAM_INT);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query, ['codigo'=>$codigo]));

			return $stmt->fetchObject(get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}
	}
}