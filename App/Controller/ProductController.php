<?php

namespace App\Controller;

use \Lib\Core\Request;
use \Lib\Database\Record;
use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Core\Uploader;
use \Lib\Utilities\Paginator;
use \App\Model\Category;
use \App\Model\Product;
use \App\Model\ImageProduct;
use \App\Model\ItemSale;
use \App\Model\Cart;
use \App\Model\CartItem;
use \App\View\Engine;

use \App\Controller\Controller;

class ProductController extends Controller{
	

	public function list(){

		try{
			
			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			if(isset($get['categoria'])){

				if(isset($get["remove"])){

				}
				$this->findByCategory();
				return;
			}

			$product = new Product();

			$paginator = new Paginator($product->count(), 15);

			$paginator->setNumberLinks(5);

			$products = $product->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());


			Transaction::close();	
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		//return json_encode($products);

		
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

			$product = new Product();
			$category = new Category();
			$categories = $category->all();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			if(!empty($get['id'])){

				$product = $product->find($get['id']);
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
		'product' => $product,
		'message' => ($message ?? '')
		]);	
	}

	public function remove(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$product = new Product();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$paginator = new Paginator($product->count(), 15);

			$paginator->setNumberLinks(5);

			$products = $product->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());
			

			if(!empty($cart->getCartItems() )){	

				throw new \Exception("Impossível remover Produto com Carrinho de Compras cheio!");
			}

			$itemSale = new ItemSale();

			if(!empty($itemSale->findByProduto($get['id']))){

				throw new \Exception("Impossível remover, Existem vendas com esse produto!");
			}

			$product->delete($get['id']);

			$imageProduct = new ImageProduct();

			$images = $imageProduct->findByProduto($get['id']);

			if($images){
				foreach($images as $image){
				
			 	 $image->delete();
				}
			}

			$paginator = new Paginator($product->count(), 15);

			$paginator->setNumberLinks(5);

			$products = $product->getProdutoCategoria($paginator->getLimit(), $paginator->getOffset());

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
			$category = new Category();
			$categories = $category->all();

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
					
			$finded = $product->findByCodigo($post['codigo']);

			if(!empty($finded)){

				throw new \Exception("O produto informado já existe!");
			}

			$product->codigo = $post['codigo'];
			$product->id_categoria = $post['id_categoria'];
			$product->descricao = $post['descricao'];
			$product->preco_custo = $post['preco_custo'];
			$product->preco_venda = $post['preco_venda'];
			$product->estoque = $post['estoque'];	

			$product->store(); 

			//UPLOAD DE IMAGENS DO PRODUTO//

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

			$product = new Product();
			$category = new Category();
			$categories = $category->all();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$product = $product->find($get['id']);
		
			if(empty($product)){

				throw new \Exception("Produto não encontrado!");		
			}

			$cart = new Cart();

			if(!empty($cart->getCartItems() )){

				throw new \Exception("Impossível atualizar Produto com Carrinho de Compras cheio!");
			}
			
			$product->codigo = $product->codigo;
			$product->id_categoria = $post['id_categoria'];
			$product->descricao = $post['descricao'];
			$product->preco_custo = $post['preco_custo'];
			$product->preco_venda = $post['preco_venda'];
			$product->estoque = $post['estoque'];
		
			$product->store();

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

			$product = new Product();
			$products = $product->getProdutoCategoria();

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			if($get['categoria'] === null || $get === ''){	

				throw new \Exception("O Campo Categoria não pode estar vazio!");
			}

			$category = $get['categoria'];

			$paginator = new Paginator($product->findByCategoriaCount($category),15);

			$paginator->setNumberLinks(5);

			if(empty($product->findByCategoria($category)) ){

				$products = $product->findByCategoria($category, $paginator->getLimit(), $paginator->getOffset());

				throw new \Exception("Nenhum Produto encontrado com a categoria '{$category}'!");
			}

			$products = $product->findByCategoria($category, $paginator->getLimit(), $paginator->getOffset());
			
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
