<?php

namespace App\Model;

use \Lib\Database\Record;
use \Lib\Database\Transaction;

class ItemSale extends Record{

	public  const TABLE_NAME = 'item_venda';

	public function findByProduto($id_produto){

		try{

			$conn = Transaction::get();

			$query = "SELECT * FROM ".self::TABLE_NAME." WHERE id_produto = :id_produto";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":id_produto", $id_produto, \PDO::PARAM_INT);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query, ['id_produto'=>$id_produto]));

			return $stmt->fetchObject(get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}
	}

	public function findItemsByVenda($id_venda){

		try{

			$conn = Transaction::get();

			$query = "SELECT *, item_venda.id as id_item_venda FROM ".self::TABLE_NAME." INNER JOIN produto 
					  ON item_venda.id_produto = produto.id WHERE id_venda = :id_venda;";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":id_venda", $id_venda, \PDO::PARAM_INT);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query, ['id_venda'=> $id_venda]));

			return $stmt->fetchAll(\PDO::FETCH_CLASS, get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}

	}

}