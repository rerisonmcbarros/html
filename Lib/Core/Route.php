<?php

namespace Lib\Core;

use \Lib\Core\Request;

class Route{
	
	private $request;
	private $separator;
	private $routes;

	public function __construct( Request $request, $separator){

		$this->request = $request;
		$this->separator = $separator;
	}
	
	public function request(){

		return $this->request;
	}

	public function get($route, $callable){
		$method = "GET";
		$this->addRoute($method, $route, $callable);
	}

	public function post($route, $callable){

		$method = "POST";
		$this->addRoute($method, $route, $callable);
	}

	public function put($route, $callable){

		$method = "POST";
		$this->addRoute($method, $route, $callable);
	}
	
	public function delete($route, $callable){

		$method = "GET";
		$this->addRoute($method, $route, $callable);
	}

	public function getRoutes(){

		return $this->routes;
	}

	public function addRoute($method, $route, $function){

		if($function instanceof \Closure){

			$this->routes[$method][$route]= ['function' => $function]; 
		}
		else{

			$separatorLen = strlen($this->separator);

			$controller = strstr($function, $this->separator, true);
			$function = substr(strstr($function, $this->separator), $separatorLen);

			$this->routes[$method][$route] = ['controller' => $controller, 'function' => $function];
		}
	}

	public function dispatch(){

		try{

			$method = $this->request->httpMethod();

			if(empty($this->routes[$method])){

				throw new \Exception('Not Implemented!', 501);	
			}
	
			if(empty($this->routeExists($method)) ){

				throw new \Exception('Page not Found!', 404);	
			}

			$route = $this->routeExists($method);

			$function = $this->routes[$method][$route]['function'];

			if($function instanceof \Closure){

				return call_user_func($function, $this->request);
			}
		
			$controller = $this->routes[$method][$route]['controller'];

			if(!class_exists($controller)){

				throw new \Exception("Page ' {$controller} ' not Found", 404);
			}

			$object = new $controller($this->request);

			if(!method_exists($object, $function)){

				throw new \Exception("' {$controller}{$this->separator}{$function} ' not Found", 404);
			}
		
			return call_user_func([$object, $function]);
		}
		catch (\Exception $e){

			return $e->getMessage()."-".$e->getFile()."-".$e->getLine();
		}
	}

	public function routeExists($method){

		$uri = $this->request->uri();
		$explodeUri = explode("/", $uri);
		$countUri = count($explodeUri);

		foreach($this->routes[$method] as $route => $value){

			$explodeRoute = explode("/", $route);
			$countRoute = count($explodeRoute);

			if($countUri == $countRoute){

				for($i = 0; $i < $countUri; $i++){

					if(strpos($explodeRoute[$i], "{") !== false ){

						$param = substr(strstr($explodeRoute[$i], "}", true), 1);
						$value = $explodeUri[$i];

						$this->request->get = array_merge($this->request->get, [$param => $value]);

						$explodeRoute[$i] = $explodeUri[$i];	
					}
				}

				$routeImplode = implode("/", $explodeRoute);

				if($routeImplode == $uri){

					return $route;
				}
			}
		}

		return null;
	}

}
