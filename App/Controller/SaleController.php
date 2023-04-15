<?php

namespace App\Controller;

use \Exception;
use Lib\Database\Transaction;
use Lib\Log\LoggerTXT;
use Lib\Utilities\Paginator;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\Sale;
use App\View\Engine;
use App\Controller\Controller;
use App\Repository\ItemSaleRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;

class SaleController extends Controller
{
	public function index()
	{	
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$cart = new Cart();

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-cart", 
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function addCartItem()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));
			
			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$productRepository = new ProductRepository();

			if (empty($post["codigo"])) {

				throw new Exception("O campo código do produto não pode ser vazio!");
			}

			if (empty($post["quantidade"]) || $post["quantidade"] <= 0) {

				throw new Exception("O campo quantidade não pode ser vazio!");
			}

			$product = $productRepository->findByCodigo($post['codigo']);

			if (empty($product)) {

				throw new Exception("Produto não encontrado em estoque!");
			}

			$cartItem = new CartItem($product, $post["quantidade"]);

			$cart->add($cartItem);

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-cart", 
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function deleteCartItem()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$cart = new Cart();

			$cart->delete($get["id"]);

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-cart", 
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function setCartEmpty()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$cart = new Cart();

			if (!empty($cart)) {

				foreach ($cart->getCartItems() as $item) {
					$cart->delete($item);
					$cart->reset();
				}	
			}

			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-cart",
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function saleRegisterForm()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$cart = new Cart();
		
			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-register", 
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function saleRegisterSave()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$post = filter_var_array($this->request->post(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$cart = new Cart();
			$sale = new Sale();

			if (empty($cart->getCartItems())) {

				throw new Exception("Venda não registrada! Carinho vazio!");
			}

			$post['valor_total'] = $cart->getTotal();
			$sale->setData($post);
			$sale->setTotalWithDiscount();

			foreach ($cart->getCartItems() as $item) {
				$sale->addItem($item->getProduct(), $item->getQuantity());
			}

			$saleRepository = new SaleRepository();
			$saleRepository->store($sale);
			
			$message = $this->message->success("Venda registrada com sucesso!");

			$cart->reset();
		
			Transaction::close();
		
		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-register", 
			[
				'cart' => ($cart ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function saleList()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$saleRepository = new SaleRepository ();

			$paginator = new Paginator($saleRepository->count(), 15);
			$paginator->setNumberLinks(5);

			$sales = $saleRepository->all($paginator->getLimit(), $paginator->getOffset());
			
			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");

		echo $engine->render(
			"venda-list", 
			[
				'links' => ($paginator->links() ?? null),
				'valorPeriodo' => null,
				'sales' => ($sales ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function findByDate()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);
			
			$saleRepository = new SaleRepository();

			if (empty($get['data_inicial'])) {

				throw new Exception("O campo data início não pode ser vazio!");
			}

			if (empty($get['data_final'])) {

				throw new Exception("O campo data final não pode ser vazio!");
			}

			$totalResults = $saleRepository->findByDateCount($get['data_inicial'], $get['data_final']);
			
			$paginator = new Paginator($totalResults, 15);
			$paginator->setNumberLinks(5);

			$sales = $saleRepository->findByDate(
				$get['data_inicial'], $get['data_final'], 
				$paginator->getLimit(), $paginator->getOffset()
			);
			
			$valorPeriodo = $saleRepository->getValorTotalByDate($get['data_inicial'], $get['data_final']);

			if (empty($totalResults)) {

				throw new Exception("Nenhuma venda encontrada para o período informado!");
			}
			
				
			$message = $this->message->success(
				"Vendas encontradas no período de ".date("d/m/Y",strtotime($get['data_inicial']) )." à ".date("d/m/Y",strtotime($get['data_final']) )
			);

			Transaction::close();
			
		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");
		
		echo $engine->render(
			"venda-list", 
			[
				'links' => ($paginator->links() ?? null),
				'valorPeriodo' => ($valorPeriodo ?? null),
				'sales' => ($sales ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}

	public function getSaleDetails()
	{
		try {

			Transaction::open(DB_CONFIG);
			Transaction::setLogger(new LoggerTXT(__DIR__."/../../Lib/Log/log.txt"));

			$get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

			$saleRepository = new SaleRepository();
			$sale = $saleRepository->find($get['id']);

			$itemSaleRepository = new ItemSaleRepository();

			$itemsSale = $itemSaleRepository->findItemsByVenda($get['id']);

			$totalValue  = 0;

			foreach ($itemsSale as $item) {
				
				$totalValue += ($item->item_preco * $item->quantidade); 
			}
			
			Transaction::close();

		} catch (Exception $e) {

			Transaction::rollBack();
			$message = $this->message->error($e->getMessage());
		}

		$engine = new Engine(__DIR__."/../../App/public/html/");
		
		echo $engine->render(
			"venda-items-list", 
			[
				'totalValue' => ($totalValue ?? null),
				'sale' => ($sale ?? []),
				'itemsSale' => ($itemsSale ?? []),
				'message' => ($message ?? '')	
			]
		);	
	}
}
