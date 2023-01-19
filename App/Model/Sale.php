<?php

namespace App\Model;

use \Lib\Database\Record;
use \Lib\Database\Transaction;

class Sale extends Record{

	public  const TABLE_NAME = 'venda';

	
	public function setNomeCliente($nome_cliente){

		if($nome_cliente === null || $nome_cliente === ''){

			throw new \Exception("Os dados do cliente não podem ser vazios!");
		}

		if(is_numeric($nome_cliente)){

			throw new \Exception("Os dados do cliente não podem ser apenas números!");
		}

		$this->data["nome_cliente"] = $nome_cliente;
	}


	public function setDesconto($desconto){

		if( $desconto === null || $desconto === ''){

			throw new \Exception("O desconto não pode ser vazio!");
		}

		if(!is_numeric($desconto) || $desconto < 0){

			throw new \Exception("O desconto deve ser um valor numérico!");
		}

		$this->data["desconto"] = $desconto;
	}


	public function setValorTotal($valor_total){

		if($valor_total === null || $valor_total === ''){

			throw new \Exception("O valor total não pode ser vazio!");
		}

		if(!is_numeric($valor_total)){

			throw new \Exception("O valor total deve ser um valor numérico!");
		}

		$this->data["valor_total"] = $valor_total;
	}

	public function setPagamento($pagamento){

		if($pagamento === null || $pagamento === ''){

			throw new \Exception("O pagamento não pode ser vazio!");
		}

		if(is_numeric($pagamento)){

			throw new \Exception("O pagamento não pode ser um valor numérico!");
		}

		$this->data["pagamento"] = $pagamento;
	}

	public function findByDate($data_inicial, $data_final){

		try{

			$conn = Transaction::get();

			$query = "SELECT * FROM ".self::TABLE_NAME." WHERE date(data_venda) >= date(:data_inicial) AND date(data_venda) <= date(:data_final)";

			$stmt = $conn->prepare($query);

			$stmt->bindValue(":data_inicial", $data_inicial, \PDO::PARAM_STR);
			$stmt->bindValue(":data_final", $data_final, \PDO::PARAM_STR);

			$stmt->execute();

			Transaction::log($this->getQueryLog($query, ['data_inicial' => $data_inicial, 'data_final' => $data_final]) );

			return $stmt->fetchAll(\PDO::FETCH_CLASS , get_class($this));
		}
		catch(\PDOException $e){

			return $e->getMessage();
		}

	}
	
}