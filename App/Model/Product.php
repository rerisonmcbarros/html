<?php

namespace App\Model;

use Lib\Database\Record;
use Lib\Database\Transaction;

class Product extends Model
{		
	public function setCodigo( $codigo )
	{
		if( $codigo === null || $codigo === '' ) 
		{
			throw new \Exception("O campo código não pode estar vazio!");
		}
		
		if( !is_numeric( $codigo ) ) 
		{
			throw new \Exception("O campo código deve ser um valor numérico!");
		}

		$this->data['codigo'] = $codigo;		
	}

	public function setIdCategoria( $id_categoria )
	{
		if( $id_categoria === null || $id_categoria === '' ) {
			throw new \Exception("O nome da categoria não pode estar vazio!");
		}

		if( !is_numeric( $id_categoria ) ) {
			throw new \Exception("O campo categoria deve ser um valor numérico!");
		}

		$this->data['id_categoria'] = $id_categoria;
	}

	public function setDescricao( $descricao )
	{
		if( $descricao === null || $descricao === '' ) {
			throw new \Exception("A descrição não pode estar vazia!");
		}

		if( is_numeric( $descricao ) ) {
			throw new \Exception("O campo descrição não pode ser um valor numérico!");
		}

		$this->data['descricao'] = $descricao;
	}

	public function setPrecoCusto( $preco_custo )
	{
		$preco_custo = str_replace( ",", ".", $preco_custo );

		if( $preco_custo === null || $preco_custo === '' ) {
			throw new \Exception("O campo de preço de custo não pode estar vazio!");
		}

		if( !is_numeric( $preco_custo ) ) {
			throw new \Exception("O campo de preço de custo deve ser um valor numérico!");
		}

		$this->data['preco_custo'] = $preco_custo;			
	}

	public function setPrecoVenda( $preco_venda )
	{
		$preco_venda = str_replace( ",", ".", $preco_venda );

		if( $preco_venda === null || $preco_venda === '' ) {
			throw new \Exception("O campo de preço de venda não pode estar vazio!");
		}

		if( !is_numeric( $preco_venda ) ) {
			throw new \Exception("O campo de preço de venda deve ser um valor numérico!");
		}

		$this->data['preco_venda'] = $preco_venda;
	}

	public function setEstoque( $estoque )
	{
		if( $estoque === null || $estoque === '' ) {
			throw new \Exception("O campo estoque não pode estar vazio!");		
		}

		if( !is_numeric( $estoque ) && $estoque >= 0 ) {
			throw new \Exception("O campo estoque deve ser um valor numérico inteiro!");
		}

		$this->data['estoque'] = $estoque;
	}
}