<?php

namespace Lib\Database;

use \Lib\Database\Transaction;

use \App\Model\Product;

class Record implements \jsonSerializable{

	protected $data;

	public function __set($name, $value){

		$value = trim($value);

		$nameMethod = "set".str_replace("_", "", ucwords($name, "_"));

		if(method_exists($this, $nameMethod)){

			call_user_func([$this, $nameMethod], $value);
		
		}else{

			$this->data[$name] = $value;
		}
	}

	public function __get($name){

		return $this->data[$name];
	}

	public function __isset($name){

		return isset($this->data[$name]);
	}

	public function toArray(){

		return $this->data;
	}

	public function jsonSerialize(){

		return $this->data;
	}

	public function setData(array $data){

		$this->data = $data;
	}

	public function getEntity(){

		$class = get_class($this);

		return constant($class.'::TABLE_NAME');
	}

	public function find(int $id, string $columns = '*'){

		try{

			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM {$this->getEntity()} WHERE id = :id";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":id", $id, \PDO::PARAM_INT);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query,['id'=> $id]));

			return $stmt->fetchObject(get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}

	}

	public function all($limit = null, $offset = 0, string $columns = '*'){

		
		try{

			if($limit == null){

				$limit = $this->getLastId();
			}

			$conn = Transaction::get();

			$query = "SELECT {$columns} FROM {$this->getEntity()} LIMIT :limit OFFSET :offset";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
			$stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);

			$result = $stmt->execute();

			Transaction::log($this->getQueryLog($query,['limit' => (int)$limit, 'offset' => (int)$offset]));

			return $stmt->fetchAll(\PDO::FETCH_CLASS, get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();

		}

	}

	public function count(){
		try{

			$conn = Transaction::get();

			$query = "SELECT count(id) as count FROM {$this->getEntity()}";

			$stmt = $conn->prepare($query);

			$stmt->execute();

			Transaction::log($query);

			return $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

		}
		catch(\PDOException $e){

			return $e->getMessage();
		}
	}

	public function getLastId(){

		try{

			$conn = Transaction::get();

			$query = "SELECT max(id) as last FROM {$this->getEntity()}";

			$stmt = $conn->prepare($query);

			$stmt->execute();

			Transaction::log($query);

			return $stmt->fetch(\PDO::FETCH_ASSOC)['last'] ?? 0;

		}
		catch(\PDOException $e){

			return $e->getMessage();
		}
	}


	public function store(){

		try{

			if(empty($this->id)){

				$this->id = $this->getLastId() + 1;

				$columns = implode(", ", array_keys($this->data) );
				$values = ":".implode(", :", array_keys($this->data) );
			
				$query = "INSERT INTO {$this->getEntity()} ({$columns}) VALUES ({$values})";
		
			}
			else{

				$set = [];
				foreach(array_keys($this->data) as $key => $value ){

					if($value != 'id'){

						$set[$key] = $value." = :".$value;
					}	
				}
				$set = implode(", ", array_values($set) );

				$query = "UPDATE {$this->getEntity()} SET {$set} WHERE id = :id";	
			}


			$conn = Transaction::get();

			$stmt = $conn->prepare($query);

			foreach($this->data  as $key => $value){

				$type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;  
				
				$stmt->bindValue(":{$key}", $value, $type);
			}


			Transaction::log($this->getQueryLog($query, $this->data));

			return $stmt->execute();
		}
		catch(\PDOException $e){

			return var_dump($e);
		}

	}

	public function delete($id = null){

		try{

			if($id == null){

				$id = $this->id;
			}

			$conn = Transaction::get();

			$query = "DELETE FROM {$this->getEntity()} WHERE id = :id";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":id", $id, \PDO::PARAM_INT);		
			

			Transaction::log($this->getQueryLog($query, ['id'=>$id]));

			return $stmt->execute();
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}
	}

	public function getQueryLog(string $query, array $data){

		$queryLog = $query;

		krsort($data);

		foreach($data  as $key => $value){

			if(is_int($value)){

				$value = $value;
			}
			else{

				$value = "'{$value}'";
			}

			$queryLog = str_replace(":{$key}", $value, $queryLog);
		}

		return $queryLog;
	}




}

