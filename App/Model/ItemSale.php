<?php

namespace App\Model;

class ItemSale extends Model
{
	public function setIdVenda($idVenda)
	{
		$this->data['id_venda'] = $idVenda;
	}

	public function setIdProduto($idProduto)
	{
		$this->data['id_produto'] = $idProduto;
	}

	public function setItemPreco($itemPreco)
	{
		$this->data['item_preco'] = $itemPreco;
	}

	public function setQuantidade($quantidade)
	{
		$this->data['quantidade'] = $quantidade;
	}
}