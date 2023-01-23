<?php

namespace App\Controller;

use \Lib\Core\Request;
use \Lib\Core\Session;
use \Lib\Database\Record;
use \Lib\Database\Transaction;
use \Lib\Log\LoggerTXT;
use \Lib\Utilities\Paginator;
use \App\Model\Product;
use \App\Model\Cart;
use \App\Model\CartItem;
use \App\Model\ItemSale;
use \App\Model\Sale;
use \App\View\Engine;
use \App\Controller\Controller;


class SaleController extends Controller{
	

	public function index(){
		
		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$cart = new Cart();

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}
		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-cart", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	
	}

	public function addCartItem(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$product = new Product();

			if(empty($post["codigo"])){

				throw new \Exception("O campo código do produto não pode ser vazio!");
			}

			if(empty($post["quantidade"]) || $post["quantidade"] <= 0){

				throw new \Exception("O campo quantidade não pode ser vazio!");
			}

			$product = $product->findByCodigo($post['codigo']);

			if(empty($product)){

				throw new \Exception("Produto não encontrado em estoque!");
			}

			$cartItem = new CartItem($product, $post["quantidade"]);

			$cart->add($cartItem);

			Transaction::close();

		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-cart", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	
	}


	public function deleteCartItem(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$cart->delete($get["id"]);

			Transaction::close();

		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-cart", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	

	}

	public function setCartEmpty(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$cart = new Cart();

			if(!empty($cart)){

				foreach($cart->getCartItems() as $item => $value){

					$cart->delete($item);
					$cart->reset();
				}	
			}

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-cart", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	

	}

	public function saleRegisterForm(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$cart = new Cart();
		
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-register", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	

	}

	public function saleRegisterSave(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$cart = new Cart();

			$sale = new Sale();

			if(empty($cart->getCartItems())){

				throw new \Exception("Venda não registrada! Carinho vazio!");
			}

			$sale->nome_cliente = $post['nome_cliente'];
			$sale->desconto = $post['desconto'];
			$sale->valor_total = $cart->getTotal() -($cart->getTotal() * ($post['desconto']/100));
			$sale->pagamento = $post['pagamento'];

			$sale->store();

			foreach ($cart->getCartItems() as $item) {
				
				$saleItem = new ItemSale();

				$saleItem->id_venda = $sale->id;
				$saleItem->id_produto = $item->getProduct()->id;
				$saleItem->item_preco = $item->getProduct()->preco_venda;
				$saleItem->quantidade = $item->getQuantity();

				$item->getProduct()->estoque -= $item->getQuantity();

				$item->getProduct()->store();

				$saleItem->store();
			}
			
			$message = $this->message->success("Venda registrada com sucesso!");

			$cart->reset();
		
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-register", [
		'cart' => ($cart ?? []),
		'message' => ($message ?? '')	
		]);	

	}


	public function saleList(){

		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$sale = new Sale();

			$sales = $sale->all();

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render("venda-list", [
		'valorPeriodo' => null,
		'sales' => ($sales ?? []),
		'message' => ($message ?? '')	
		]);	

	}

	public function findByDate(){


		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$sale = new Sale();

			if(empty($post['data_inicial'])){

				throw new \Exception("O campo data início não pode ser vazio!");
			}

			if(empty($post['data_final'])){

				throw new \Exception("O campo data final não pode ser vazio!");
			}

			$sales = $sale->findByDate($post['data_inicial'], $post['data_final']);

			if(!empty($sales)){

				$valorPeriodo = 0;

				foreach($sales as $sale){

					$valorPeriodo += $sale->valor_total;
				}
			}

			if(empty($sales)){

				$sales = $sale->all();

				throw new \Exception("Nenhuma venda encontrada para o período informado!");
			}
			
			$message = $this->message->success(
				"Vendas encontradas no período de ".date("d/m/Y",strtotime($post['data_inicial']) )." à ".date("d/m/Y",strtotime($post['data_final']) ));

			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");
		
		echo $engine->render("venda-list", [
		'valorPeriodo' => ($valorPeriodo ?? null),
		'sales' => ($sales ?? []),
		'message' => ($message ?? '')	
		]);	
	}


	public function getSaleDetails(){


		try{

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$sale = (new Sale())->find( $get['id'] );

			$itemSale = new ItemSale();

			$itemsSale = $itemSale->findItemsByVenda($get['id']);

			$totalValue  = 0;

			foreach ($itemsSale as $item) {
				
				$totalValue += ($item->item_preco * $item->quantidade); 
			}
			
			Transaction::close();
		}
		catch(\Exception $e){

			Transaction::rollBack();
			$message = $this->message->error( $e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");
		
		echo $engine->render("venda-items-list", [
		'totalValue' => $totalValue ?? null,
		'sale' => ($sale ?? []),
		'itemsSale' => ($itemsSale ?? []),
		'message' => ($message ?? '')	
		]);	
	}
}	

