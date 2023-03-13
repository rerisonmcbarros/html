<?php

namespace App\Repository;

use App\Model\Category;
use Lib\Database\Mapper;
use Lib\Database\Transaction;

class CategoryRepository extends Mapper
{
	public function __construct()
	{
		parent::__construct( new Category() );
	}

	public function findByCodigo( $codigo, $columns = '*' )
    {
        $conn = Transaction::get();

        $query = "SELECT {$columns} FROM {$this->getEntity()} WHERE codigo = :codigo";
        $stmt = $conn->prepare($query);
        $stmt->bindValue( ":codigo", $codigo, \PDO::PARAM_INT );

        $stmt->execute();

        Transaction::log( $this->getQueryLog( $query, ['codigo'=>$codigo] ) );

        return $stmt->fetchObject( get_class( $this->model ) );
	}
}