<?php

namespace App\Repository;

use App\Model\ItemSale;
use Lib\Database\Repository;
use \Lib\Database\Transaction;

class ItemSaleRepository extends Repository
{
	public function __construct()
	{
		parent::__construct(new ItemSale(), 'item_venda');
	}
	public function findByProduto( $id_produto )
	{ 
		$conn = Transaction::get();

		$query = "SELECT * FROM {$this->getEntity()} WHERE id_produto = :id_produto";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(":id_produto", $id_produto, \PDO::PARAM_INT);
		$stmt->execute();

		Transaction::log( $this->getQueryLog( $query, ['id_produto'=>$id_produto] ) );

		return $stmt->fetchObject( get_class( $this->model ) );
	}

	public function findItemsByVenda( $id_venda )
	{
		$conn = Transaction::get();

		$query = 
		"SELECT *, item_venda.id as id_item_venda 
		FROM {$this->getEntity()} INNER JOIN produto 
		ON item_venda.id_produto = produto.id WHERE id_venda = :id_venda;";

		$stmt = $conn->prepare( $query );
		$stmt->bindValue( ":id_venda", $id_venda, \PDO::PARAM_INT );
		$stmt->execute();

		Transaction::log( $this->getQueryLog( $query, ['id_venda'=> $id_venda] ) );

		return $stmt->fetchAll( \PDO::FETCH_CLASS, get_class( $this->model ) );
	}
}