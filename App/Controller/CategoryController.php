<?php

namespace App\Controller;

use \Exception;
use Lib\Database\Transaction;
use Lib\Log\LoggerTXT;
use Lib\Utilities\Paginator;
use App\Model\Category;
use App\View\Engine;
use App\Controller\Controller;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class CategoryController extends Controller
{	
	public function list()
	{
		try {
	
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$categoryRepository = new CategoryRepository();
			$paginator = new Paginator($categoryRepository->count(),15);
			$paginator->setNumberLinks(5);

			$categories = $categoryRepository->all($paginator->getLimit(), $paginator->getOffset());

			Transaction::close();
	
		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"categoria-list", 
			[
				'links'    => ($paginator->links() ?? null),
				'categories' => $categories,
				'message' => ($message ?? '')	
			]
		);		
	}

	public function form()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$categoryRepository = new CategoryRepository();
		
			if (!empty($get['id'])) {
				$category = $categoryRepository->find($get['id']);
			}
		
			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"categoria-form", 
			[
				'category' => ($category ?? null),
				'message' => ($message ?? '')
			]
		);	
	}

	public function update()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$categoryRepository = new CategoryRepository();

			$category = $categoryRepository->find($get['id']);

			if (empty($category)) {
				throw new Exception("Categoria não encontrada!");		
			}	
				
			$post['codigo'] = $category->codigo;
			$category->setData($post);
			
			$categoryRepository->store($category);

			$message = $this->message->success("Categoria atualizada com sucesso!");

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"categoria-form", 
			[
				'category' => $category,
				'message' => ($message ?? '')
			]
		);	
	}

	public function remove()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$categoryRepository = new CategoryRepository();

			$paginator = new Paginator($categoryRepository->count(),15);
			$paginator->setNumberLinks(5);

			$finded = $categoryRepository->find($get['id']);

			if (!empty($finded)) {

				$products = (new ProductRepository())->findByCategoria($finded->nome);
			}

			if (!empty($products)) {

				$categories = $categoryRepository->all($paginator->getLimit(), $paginator->getOffset());

				throw new Exception("Impossível remover, há Produtos com essa categoria!");
			}

			$categoryRepository->delete($get['id']);

			$message = $this->message->success("Categoria removida com sucesso!");

			$categories = $categoryRepository->all($paginator->getLimit(), $paginator->getOffset());

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"categoria-list", 
			[
				'links'  => ($paginator->links() ?? null),
				'categories' => ($categories ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function save()
	{
		try {
		
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$categoryRepository = new CategoryRepository();
					
			$finded = $categoryRepository->findByCodigo($post['codigo']);

			if (!empty($finded)) {

				throw new Exception("A Categoria informada já existe!");
			}

			$category = new Category();
			$category->setData($post);

			$categoryRepository->store($category); 

			$message = $this->message->success("Categoria '{$category->nome}' Cadastrada com sucesso!");	

			$category->setData([]);

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"categoria-form", 
			[
				'message' => ($message ?? '')
			]
		);	
	}
}
