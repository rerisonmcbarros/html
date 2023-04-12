<?php

namespace App\View;

class Macros
{
	public function numberFormatBr($value)
	{
		return number_format((float)$value,2,".",",");
	}

	public function dateFormatBr($value)
	{
		return date("d/m/Y H:i", strtotime($value));
	}
}
