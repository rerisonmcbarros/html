<?php

namespace App\Model;

use Lib\Database\Record;
use Lib\Database\Transaction;

class ImageProduct extends Record{


	public const TABLE_NAME = "imagem_produto";

	public function findByProduto($id_produto, $columns = '*'){

		try{

			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM ".self::TABLE_NAME." WHERE id_produto = :id_produto";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":id_produto", $id_produto, \PDO::PARAM_INT);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query, ['id_produto'=>$id_produto]));

			return $stmt->fetchAll(\PDO::FETCH_CLASS,get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}

	}

}