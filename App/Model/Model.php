<?php

namespace App\Model;

use Lib\Database\ModelInterface;

class Model implements ModelInterface
{
    protected $data;

	public function __set( $name, $value )
    {
		$value = trim( $value );
		$nameMethod = "set".str_replace( "_", "", ucwords( $name, "_" ) );

		if( method_exists($this, $nameMethod) )
        {
			call_user_func(  [$this, $nameMethod], $value );
		}
        else
        {
			$this->data[$name] = $value;
		}
	}

	public function __get( $name )
    {
		return $this->data[$name];
	}

    public function __isset( $name )
    {
		return isset( $this->data[$name] );
	}

    public function setData( array $data )
    {
		if( !empty( $data ) )
		{
			foreach( $data as $key => $value )
			{
				$this->$key = $value;
			}
		}
		$this->data = $data;
	}

	public function getData(): array
    {
		return $this->data;
	}

	public function getEntity(): string
	{
		return get_class($this)::$entity;
	}
}