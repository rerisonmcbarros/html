<?php

namespace App\Repository;

use \PDO;
use App\Model\Sale;
use Lib\Database\Repository;
use Lib\Database\Transaction;
use Lib\Database\ModelInterface;
use App\Repository\ProductRepository;
use App\Repository\ItemSaleRepository;
use Exception;

class SaleRepository extends Repository
{
	public function __construct()
	{
		parent::__construct(new Sale(), 'venda');
	}

	public function findByDate($data_inicial, $data_final, $limit = null, $offset = 0)
	{
		if ($limit == null) {
			$limit = $this->getLastId();
		}

		$conn = Transaction::get();

		$query =
		"SELECT * FROM {$this->getEntity()}
			WHERE date(data_venda) >= date(:data_inicial) 
		AND date(data_venda) <= date(:data_final) 
		LIMIT :limit OFFSET :offset";

		$stmt = $conn->prepare($query);

		$stmt->bindValue(":data_inicial", $data_inicial, PDO::PARAM_STR);
		$stmt->bindValue(":data_final", $data_final, PDO::PARAM_STR);
		$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
		$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

		$stmt->execute();

		Transaction::log(
			$this->getQueryLog($query, [
				'data_inicial' => $data_inicial, 
				'data_final' => $data_final, 
				'limit' => (int) $limit,
				'offset' =>  (int) $offset
			])
		);

		return $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this->model));
	}

	public function findByDateCount($data_inicial, $data_final)
	{
		$conn = Transaction::get();

		$query =
		"SELECT count(id) as total_results_period 
		FROM {$this->getEntity()}
			WHERE date(data_venda) >= date(:data_inicial) 
		AND date(data_venda) <= date(:data_final)";

		$stmt = $conn->prepare($query);

		$stmt->bindValue(":data_inicial", $data_inicial, PDO::PARAM_STR);
		$stmt->bindValue(":data_final", $data_final, PDO::PARAM_STR);

		$stmt->execute();

		Transaction::log(
			$this->getQueryLog($query, ['data_inicial' => $data_inicial, 'data_final' => $data_final])
		);

		return $stmt->fetch(PDO::FETCH_ASSOC)['total_results_period'] ?? null;
	}

	public function getValorTotalByDate($data_inicial, $data_final)
	{
		$conn = Transaction::get();

		$query =
		"SELECT sum(valor_total) as valor_total_periodo 
		FROM {$this->getEntity()}
			WHERE date(data_venda) >= date(:data_inicial) 
		AND date(data_venda) <= date(:data_final)";

		$stmt = $conn->prepare($query);

		$stmt->bindValue(":data_inicial", $data_inicial, PDO::PARAM_STR);
		$stmt->bindValue(":data_final", $data_final, PDO::PARAM_STR);

		$stmt->execute();

		Transaction::log(
			$this->getQueryLog($query, ['data_inicial' => $data_inicial, 'data_final' => $data_final])
		);

		return $stmt->fetch(PDO::FETCH_ASSOC)['valor_total_periodo'] ?? null;
	}

	public function store(ModelInterface $model)
	{
		if (!($model instanceof Sale)) {
			throw new Exception('The model must be an instance of Sale');
		}

		$conn = Transaction::get();

		$this->model = $model;
		$this->model->id = $this->getLastId() + 1;
		$columns = implode(", ", array_keys($this->getData()));
		$values = ":".implode(", :", array_keys($this->getData()));
		$query = "INSERT INTO {$this->getEntity()} ({$columns}) VALUES ({$values})";
		
		$stmt = $conn->prepare($query);
		
		foreach ($this->getData()  as $key => $value) {

			$type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;  	
			$stmt->bindValue(":{$key}", $value, $type);
		}

		Transaction::log( 
			$this->getQueryLog($query, $this->getData()) 
		);

		$stmt->execute();
	
		foreach ($this->model->getItems() as $item) {
			$itemRepository = new ItemSaleRepository($item);
			$item->id_venda = $this->model->id;
			$itemRepository->store($item);

			$productRepository = new ProductRepository();
			$product = $productRepository->find($item->id_produto);
			$product->reduceStorage($item->quantidade);
			$productRepository->store($product);
		}

		return true;
	}

}
