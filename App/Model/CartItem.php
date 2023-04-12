<?php

namespace App\Model;

use \Exception;
use App\Model\Product;

class CartItem
{	
	private $product;
	private $quantity;

	public function __construct(Product $product, $quantity)
	{
		$this->setProduct($product);
		$this->setQuantity($quantity);
	}

	public function setProduct($product)
	{
		if (is_null($product)) {

			throw new Exception("O produto não pode ser vazio!");
		}

		$this->product = $product;
	}

	public function setQuantity($quantity)
	{
		if (is_null($quantity) || $quantity <= 0) {

			throw new Exception("A quantidade não pode ser vazio!");
		}
		if (!is_numeric($quantity)) {

			throw new Exception("A quantidade deve ser um valor numérico");
		}

		$this->quantity = $quantity;
	}

	public function getProduct()
	{
		return $this->product;
	}

	public function getQuantity()
	{
		return (int) $this->quantity;
	}

	public function getSubTotal()
	{
		return $this->product->preco_venda * $this->quantity;
	}
}
