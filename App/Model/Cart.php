<?php

namespace App\Model;

use \Exception;
use Lib\Core\Session;

class Cart
{	
	private $items;
	private $session;

	public function __construct()
	{
		$this->session = new Session();
		$this->items = $this->restore();
	}

	public function __destruct()
	{
		$this->session->add('cart', serialize($this->items));
	}

	private function restore()
	{
		return isset($this->session->cart) ? unserialize($this->session->cart) : [];
	}

	public function add(CartItem $item)
	{
		$id = $item->getProduct()->id;

		if ($item->getProduct()->estoque < $item->getQuantity()) {

			throw new Exception("Estoque de '{$item->getProduct()->descricao}' é insuficiente!");
		}

		$this->items[$id] = $item;	
	}

	public function delete($id)
	{
		if (isset($this->items[$id])) {

			unset($this->items[$id]);
		}
	}

	public function getTotal()
	{
		$total = 0;

		foreach ($this->items as $item) {
			
			$total += $item->getSubTotal();
		}

		return $total;
	}

	public function getCartItems()
	{
		return $this->items;
	}

	public function reset()
	{
		$this->session->destroy();
		$this->items = $this->restore();
	}	
}
