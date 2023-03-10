<?php

namespace Lib\Database;

use \Lib\Database\Transaction;

class Mapper 
{
    protected $model;

    public function __construct( ModelInterface $model )
    {
        $this->model = $model;
    }

	protected function getEntity()
    {
		return $this->model->getEntity();
	}

	public function getData()
	{
		return $this->model->getData();
	}

	public function find( int $id, string $columns = '*' )
    {
		try
        {
			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM {$this->getEntity()} WHERE id = :id";
			$stmt = $conn->prepare( $query );
			$stmt->bindValue( ":id", $id, \PDO::PARAM_INT );
			$stmt->execute();

			Transaction::log( $this->getQueryLog( $query,['id'=> $id] ) );

			return $stmt->fetchObject( get_class( $this->model ) );
		}
		catch( \PDOException $e )
        {
			return $e->getMessage();
		}
	}

	public function all( $limit = null, $offset = 0, string $columns = '*' )
	{
		try
		{
			if($limit == null)
			{
				$limit = $this->getLastId();
			}
			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM {$this->getEntity()} LIMIT :limit OFFSET :offset";

			$stmt = $conn->prepare( $query );

			$stmt->bindValue( ":limit", $limit, \PDO::PARAM_INT );
			$stmt->bindValue( ":offset", $offset, \PDO::PARAM_INT );
            $stmt->execute();

			Transaction::log( $this->getQueryLog( $query,['limit' => (int)$limit, 'offset' => (int)$offset] ) );

			return $stmt->fetchAll( \PDO::FETCH_CLASS, get_class( $this->model ) );
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
	}

	public function count()
	{
		try
		{
			$conn = Transaction::get();

			$query = "SELECT count(id) as count FROM {$this->getEntity()}";
			$stmt = $conn->prepare( $query );
			$stmt->execute();

			Transaction::log( $query );

			return $stmt->fetch( \PDO::FETCH_ASSOC )['count'];
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
	}

	public function getLastId()
	{
		try
		{
			$conn = Transaction::get();

			$query = "SELECT max(id) as last FROM {$this->getEntity()}";
			$stmt = $conn->prepare( $query );
			$stmt->execute();

			Transaction::log( $query );

			return $stmt->fetch( \PDO::FETCH_ASSOC )['last'] ?? 0;
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
	}

	public function store(ModelInterface $model)
	{
		try
		{
			$this->model = $model;

			if( empty( $this->model->id ) )
			{
				$this->model->id = $this->getLastId() + 1;
				$columns = implode( ", ", array_keys( $this->getData() ) );
				$values = ":".implode( ", :", array_keys( $this->getData() ) );
				$query = "INSERT INTO {$this->getEntity()} ({$columns}) VALUES ({$values})";
			}
			else
			{
				$set = [];
				foreach( array_keys( $this->getData() ) as $key => $value )
				{
					if( $value != 'id' )
					{
						$set[$key] = $value." = :".$value;
					}	
				}
				$set = implode( ", ", array_values( $set ) );
				$query = "UPDATE {$this->getEntity()} SET {$set} WHERE id = :id";	
			}
			$conn = Transaction::get();
			$stmt = $conn->prepare( $query );

			foreach( $this->getData()  as $key => $value )
			{
				$type = is_int( $value ) ? \PDO::PARAM_INT : \PDO::PARAM_STR;  	
				$stmt->bindValue( ":{$key}", $value, $type );
			}

			Transaction::log( $this->getQueryLog( $query, $this->getData() ) );

			return $stmt->execute();
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
	}

	public function delete( $id = null )
	{
        if( empty( $id ) )
        {
            $id = $this->model->id;
        }
		try
		{
			$conn = Transaction::get();

			$query = "DELETE FROM {$this->getEntity()} WHERE id = :id";
			$stmt = $conn->prepare( $query );
			$stmt->bindValue( ":id", $id, \PDO::PARAM_INT );		
			
			Transaction::log( $this->getQueryLog( $query, ['id'=>$id] ) );

			return $stmt->execute();
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
	}

	protected function getQueryLog( string $query, array $data )
	{
		$queryLog = $query;

		krsort( $data );
		foreach( $data  as $key => $value )
		{
			if( is_int( $value ) )
			{
				$value = $value;
			}
			else
			{
				$value = "'{$value}'";
			}

			$queryLog = str_replace( ":{$key}", $value, $queryLog );
		}

		return $queryLog;
	}
}

