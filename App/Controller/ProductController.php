<?php

namespace App\Controller;

use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Utilities\Paginator;
use \App\Model\Product;
use \App\Model\ItemSale;
use \App\Model\Cart;
use \App\View\Engine;
use \App\Controller\Controller;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class ProductController extends Controller
{
	public function list()
	{
		try{	
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			if(isset($get['categoria'])){
				$this->findByCategory();
				return;
			}

			$productRepository = new ProductRepository();

			$paginator = new Paginator($productRepository->count(), 15);

			$paginator->setNumberLinks(5);

			$products = $productRepository->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());

			Transaction::close();	
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-list", [

		'links'    => $paginator->links() ?? null,
		'title'    => "Listagem de Produtos",
		'products' => $products,
		'message'  => ($message ?? '')
		]);	
	}

	public function form(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$productRepository = new ProductRepository();
			$categoryRepository = new CategoryRepository();
			$categories = $categoryRepository->all();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			if(!empty($get['id'])){

				$product = $productRepository->find($get['id']);
			}

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-form", [
		'categories' => $categories,
		'product' => ($product ?? null),
		'message' => ($message ?? '')
		]);	
	}

	public function remove(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$productRepository = new ProductRepository();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$paginator = new Paginator($productRepository->count(), 15);

			$paginator->setNumberLinks(5);

			if(!empty($cart->getCartItems() )){	
				$products = $productRepository->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());
			
				throw new \Exception("Impossível remover Produto com Carrinho de Compras cheio!");
			}

			$itemSale = new ItemSale();

			if(!empty($itemSale->findByProduto($get['id']))){
				$products = $productRepository->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());
			
				throw new \Exception("Impossível remover, Existem vendas com esse produto!");
			}

			$productRepository->delete($get['id']);
			/*
			$imageProduct = new ImageProduct();

			$images = $imageProduct->findByProduto($get['id']);

			if($images){
				foreach($images as $image){
				
			 	 $image->delete();
				}
			}
			*/
	
			$products = $productRepository->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());

			$message = $this->message->success("Produto removido com sucesso!");	

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-list", [
		'links'   => $paginator->links() ?? null,
		'products' => ($products ?? []),
		'message' => ($message ?? '')	
		]);	
	}

	public function save(){

		try{
		
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$product = new Product();
			$productRepository = new ProductRepository();
			$categoryRepository = new CategoryRepository();
			$categories = $categoryRepository->all();

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
					
			$finded = $productRepository->findByCodigo($post['codigo']);

			if(!empty($finded)){

				throw new \Exception("O produto informado já existe!");
			}

			$product->setData($post);

			$productRepository->store($product); 

			//UPLOAD DE IMAGENS DO PRODUTO//
			/*
			if(!empty($_FILES['file']['name'][0])){

				$uploader = new Uploader($_FILES['file'], true);

				$uploader->setAllowedTypes(['image/jpg', "image/png", "image/jpeg"]);

				$uploader->setPath(__DIR__."/../public/upload/produto/");

				$uploader->moveUploadedFile();

				$images = $uploader->files();

				if(!empty($images)){

					foreach($images as $image){

						$imageProduct = new ImageProduct();

						$imageProduct->id = null;
						$imageProduct->nome_imagem = $image['new_name'];
						$imageProduct->id_produto = $product->id;

						$imageProduct->store();
					}	
				}
			}
			*/
			$message = $this->message->success("Produto '{$product->descricao}' Cadastrado com sucesso!");

			$product->setData([]);
			
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-form", [
		'categories' => $categories,
		'message' => ($message ?? '')
		]);	
	}

	public function update(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$productRepository = new ProductRepository();
			$categoryRepository = new CategoryRepository();
			$categories = $categoryRepository->all();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$product = $productRepository->find($get['id']);
		
			if(empty($product)){

				throw new \Exception("Produto não encontrado!");		
			}

			$cart = new Cart();

			if(!empty($cart->getCartItems() )){

				throw new \Exception("Impossível atualizar Produto com Carrinho de Compras cheio!");
			}
			
			$post['codigo'] = $product->codigo;

			$product->setData($post);
			
			$productRepository->store($product);

			$message = $this->message->success("Produto atualizado com sucesso!");	
	
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-form", [
		'categories' => $categories,
		'product' => $product,
		'message' => ($message ?? '')
		]);	
	}

	public function findByCategory(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$productRepository = new ProductRepository();
			$products = $productRepository->getProdutoCategoria();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
		
			if($get['categoria'] === null || $get === ''){	

				throw new \Exception("O Campo Categoria não pode estar vazio!");
			}

			$category = $get['categoria'];

			$paginator = new Paginator($productRepository->findByCategoriaCount($category),15);

			$paginator->setNumberLinks(5);

			if(empty($productRepository->findByCategoria($category)) ){

				$products = $productRepository->findByCategoria($category, $paginator->getLimit(), $paginator->getOffset());

				throw new \Exception("Nenhum Produto encontrado com a categoria '{$category}'!");
			}

			$products = $productRepository->findByCategoria($category, $paginator->getLimit(), $paginator->getOffset());
			
			$message = $this->message->success("Produtos encontrados na categoria '{$category}'");	

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		
		$engine = new Engine(__DIR__."/../public/html/");

		echo $engine->render("produto-list", [
		'links'  => isset($paginator) ? $paginator->links() : null,
		'products' => $products,
		'message' => ($message ?? '')	
		]);		
	}
}