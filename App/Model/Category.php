<?php

namespace App\Model;

class Category extends Model
{
	public function setCodigo( string $codigo )
	{
		if( $codigo === null || $codigo === '' )
		{
			throw new \Exception("O código da categoria não pode ser vazio!");
		}

		if( !is_numeric( $codigo ) )
		{
			throw new \Exception("O código da categoria deve ser um valor numérico!");
		}

		$this->data['codigo'] = $codigo;
	}

	public function setNome( string $nome )
	{
		if( $nome === null || $nome === '' )
		{
			throw new \Exception("O nome da categoria não pode estar vazio!");
		}

		if( is_numeric( $nome ) )
		{
			throw new \Exception("O nome da categoria não pode ser um valor numérico!");
		}

		$this->data['nome'] = $nome;
	}	
}