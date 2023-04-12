<?php

namespace Lib\Core;

class AplicationLoader
{
	private $class;
	private $documentRoot;

	public function __construct($documentRoot)
	{
		$this->documentRoot = $documentRoot;
	}

	public function load($class)
	{
		$this->class = str_replace('\\', '/',$class);
		$className = $this->getClassName();
		$namespace = $this->getNamespace();
		$pathRoot = $this->getPathRoot();

		$class = $pathRoot.$namespace.$className.".php";

		if ($this->classExists($class)) {

			require_once $class;
			return;
		}
	}

	private function getClassName()
	{
		$className = substr(strrchr($this->class, '/'), 1);
		
		return  ($className == false) ? $this->class : $className ;
	}

	private function getNamespace()
	{
		$classNamePos= strrpos($this->class, "/");
		$namespace = substr($this->class, 0, $classNamePos+1);
		$rootLength = strlen($this->documentRoot."/");

		$base = substr(strstr(__DIR__,$this->documentRoot), $rootLength).'/';

		return ($namespace == false) ? $base : $namespace;
	}

	private function getPathRoot()
	{
		return strstr(__DIR__, $this->documentRoot, true).$this->documentRoot."/";
	}

	private function classExists($class)
	{
		if (file_exists($class) && is_file($class)) {

			return true;
		}

		return false;
	}
}
