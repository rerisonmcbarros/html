<?php

namespace App\Model;

class Sale extends Model
{ 	
	private $items;

	public function __construct()
	{
		$this->items = [];
	}

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

	public function setTotalWithDiscount()
	{
		$this->data['valor_total'] = $this->valor_total - ($this->valor_total*($this->desconto/100));
	}

	public function addItem(Product $product, $quantity)
	{
		$itemSale = new ItemSale();
		$itemSale->id_produto;
		$itemSale->item_preco;
		$itemSale->quantidade;

		$this->items[] = $itemSale;
	}
	public function getItems()
	{
		return $this->items;
	}
}