<?php

namespace App\Controller;

use \Lib\Core\Request;
use \Lib\Database\Record;
use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Utilities\Paginator;
use \App\Model\Category;
use \App\Model\Product;
use \App\View\Engine;

use \App\Controller\Controller;

class CategoryController extends Controller{
	
	
	public function list(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$category = new Category();
			$paginator = new Paginator($category->count(),15);
			$paginator->setNumberLinks(5);

			$categories = $category->all($paginator->getLimit(), $paginator->getOffset());

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("categoria-list", [
		'links'    => $paginator->links() ?? null,
		'categories' => $categories,
		'message' => ($message ?? '')	
		]);	
		
	}

	public function form(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$category = new Category();
		
			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			if(!empty($get['id'])){

				$category = $category->find($get['id']);
			}
		
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("categoria-form", [
		'category' => $category,
		'message' => ($message ?? '')
		]);	
	}

	public function update(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$category= new Category();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$category = $category->find($get['id']);

			if(empty($category)){

				throw new \Exception("Categoria não encontrado!");		
			}		
			
			$category->codigo = $category->codigo;
			$category->nome = $post['nome'];
			
			$category->store();

			$message = $this->message->success("Categoria atualizada com sucesso!");

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("categoria-form", [
		'category' => $category,
		'message' => ($message ?? '')
		]);	
	}

	public function remove(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$category = new Category();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$paginator = new Paginator($category->count(),15);
			$paginator->setNumberLinks(5);

			$finded = $category->find($get['id']);

			if(!empty($finded)){

				$products = (new Product())->findByCategoria($finded->nome);
			}

			if(!empty($products)){

				$categories = $category->all($paginator->getLimit(), $paginator->getOffset());

				throw new \Exception("Impossível remover, há Produtos com essa categoria!");
			}

			$category->delete($get['id']);

			$message = $this->message->success("Categoria removida com sucesso!");

			$categories = $category->all($paginator->getLimit(), $paginator->getOffset());

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("categoria-list", [
	 	'links'  => $paginator->links() ?? null,
		'categories' => ($categories ?? []),
		'message' => ($message ?? '')	
		]);	
		
	}

	public function save(){

		try{
		
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$category = new Category();

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
					
			$finded = $category->findByCodigo($post['codigo']);

			if(!empty($finded)){

				throw new \Exception("A Categoria informada já existe!");
			}

			$category->codigo = $post['codigo'];
			$category->nome = $post['nome'];
			
			$category->store(); 

			$message = $this->message->success("Categoria '{$category->nome}' Cadastrada com sucesso!");	

			$category->setData([]);

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("categoria-form", [
		'message' => ($message ?? '')
		]);	
	}

}


