<?php

namespace App\View;

use \Exception;
use App\View\Macros;

class Engine
{	
	private $filePath;
	private $extension;
	private $layout;
	private $data;
	private $replaces;
	private $section;
	private $currentSection;

	private $dependency;

	public function __construct($filePath, $extension = ".php")
	{
		$this->filePath = $filePath;
		$this->extension = $extension;
		$this->dependency = new Macros();
		$this->replaces = [];
	}

	public function __call($name, $value)
	{
		$class = get_class($this->dependency);

		if (!in_array($name, get_class_methods($class))) {

			throw new Exception("Method {$name} does not exists on {$class}");
		}

		return call_user_func_array([$this->dependency, $name], $value);
	}

	public function layout(string $filename, $data = [])
	{
		$this->layout = $filename;
		$this->data = $data;
	}
	
	public function section(string $name)
	{
		return $this->section[$name];
	}

	public function start($name)
	{
		ob_start();
		$this->currentSection = $name;
	}

	public function stop()
	{
		$this->section[$this->currentSection] = ob_get_contents();
		ob_end_clean();
	}

	public function render(string $filename, array $data = [], array $replaces = [])
	{
		$this->replaces = $replaces;
		$file = $this->filePath.$filename.$this->extension;

		if (!empty($data) && is_array($data)) {

			extract($data);
		}
	
		if (!file_exists($file)) {

			throw new Exception("File '{$filename}{$this->extension}' not Found on {$this->filePath}!");
		}

		ob_start();

		require_once $file;

		$content = ob_get_contents();

		ob_end_clean();

		$this->section['content'] = $content;

		if (!empty($this->layout)) {

			$layout = $this->layout;

			$this->layout = null;
			
			$data = array_merge($this->data, $data);
			
			return $this->render($layout,$data, $replaces);
		}

		if (!empty($this->replaces)) {

			foreach ($this->replaces as $replace => $value) {
				$content = str_replace("{{{$replace}}}", $value, $content);
			}
		}

		return $content;
	}
}
