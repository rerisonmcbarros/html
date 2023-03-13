<?php

namespace App\Repository;

use App\Model\Product;
use Lib\Database\Mapper;
use Lib\Database\Transaction;

class ProductRepository extends Mapper
{
	public function __construct()
	{
		parent::__construct( new Product() );
	}

	public function findByCodigo( string $codigo, string $columns = '*' )
	{	
		$conn = Transaction::get();

		$query = "SELECT {$columns} FROM {$this->getEntity()} WHERE codigo = :codigo";
		$stmt = $conn->prepare( $query );
		$stmt->bindValue( ":codigo", $codigo, \PDO::PARAM_STR );
		$stmt->execute();

		Transaction::log( $this->getQueryLog( $query, ['codigo'=>$codigo] ) );

		return $stmt->fetchObject( get_class( $this->model ) );	
	}

	public function findByCategoria( string $nome, $limit = null, $offset = 0 )
	{
		if( $limit == null )
		{
			$limit = $this->findByCategoriaCount( $nome );
		}
		$conn = Transaction::get();

		$query = "SELECT 
		produto.id,
		produto.codigo,
		produto.descricao,
		produto.preco_custo,
		produto.preco_venda,
		produto.estoque,
		categoria.codigo as codigo_categoria,
		categoria.nome as nome_categoria  
		FROM {$this->getEntity()} INNER JOIN categoria 
		ON produto.id_categoria = categoria.id WHERE categoria.nome 
		LIKE CONCAT('%',:nome, '%') LIMIT :limit OFFSET :offset";

		$stmt = $conn->prepare( $query );

		$stmt->bindValue( ":nome", $nome, \PDO::PARAM_STR );
		$stmt->bindValue( ":limit", $limit, \PDO::PARAM_INT );
		$stmt->bindValue( ":offset", $offset, \PDO::PARAM_INT );

		$stmt->execute();

		Transaction::log( $this->getQueryLog( $query, ['nome'=> $nome] ) );

		return $stmt->fetchAll( \PDO::FETCH_CLASS , get_class( $this->model ) );
	}	

	public function findByCategoriaCount( string $nome ) 
	{
		$conn = Transaction::get();

		$query = "SELECT count(categoria.nome) as count FROM {$this->getEntity()} INNER JOIN categoria 
		ON produto.id_categoria = categoria.id WHERE categoria.nome LIKE CONCAT('%',:nome, '%') ";
		
		$stmt = $conn->prepare( $query );
		$stmt->bindValue( ":nome", $nome, \PDO::PARAM_STR );
		$stmt->execute();

		Transaction::log( $this->getQueryLog( $query, ['nome'=> $nome] ) );

		return $stmt->fetch( \PDO::FETCH_ASSOC )['count'] ?? null;
	}

	public function getProdutoCategoria( $limit = null, $offset = 0 )
	{
		if($limit == null)
		{
			$limit = $this->getLastId();
		}
		$conn = Transaction::get();

		$query = "SELECT 
		produto.id,
		produto.codigo,
		produto.descricao,
		produto.preco_custo,
		produto.preco_venda,
		produto.estoque,
		categoria.nome as nome_categoria  
		FROM {$this->getEntity()} INNER JOIN categoria 
		ON produto.id_categoria = categoria.id ORDER BY produto.id LIMIT :limit OFFSET :offset";

		$stmt = $conn->prepare( $query );
		$stmt->bindValue( ":limit", $limit, \PDO::PARAM_INT );
		$stmt->bindValue( ":offset", $offset, \PDO::PARAM_INT );
		$stmt->execute();

		Transaction::log( $query );

		return $stmt->fetchAll( \PDO::FETCH_CLASS , get_class( $this->model ) );
	}
}