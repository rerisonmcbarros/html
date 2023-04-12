<?php

namespace Lib\Log;

class LoggerTXT extends Logger
{	
	public function write($message)
	{
		date_default_timezone_set("America/Sao_Paulo");
		$date = date("Y-m-d H:i:s");
		$text  = "{$date} ## {$message}\n\n";

		$handle = fopen($this->filePath, "a");
		fwrite($handle, $text);
		fclose($handle);
	}
}
