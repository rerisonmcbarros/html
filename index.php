<?php 


require_once __DIR__.'/autoload.php';

use \Lib\Core\Request;
use \Lib\Core\Route;

$request = new Request(DOCUMENT_ROOT);

$route = new Route($request, "::");

// TESTE DE ROTAS //
/*
$route->get("/produto/{id}/{categoria}", function($callback){

echo "<pre>", var_dump($callback), "</pre>";

echo "Rota encontrada!";

});
*/

// ROTA PRINCIPAL //

$route->get("/", "\App\Controller\PageController::home");

// ROTAS PRODUTO //
$route->get("/produto/list", "\App\Controller\ProductController::list");
$route->post("/produto/list", "\App\Controller\ProductController::findByCategory");

$route->get("/produto/{id}/remove", "\App\Controller\ProductController::remove");
//$route->post("/produto/{id}/remove", "\App\Controller\ProductController::findByCategoria");

$route->get("/produto/create", "\App\Controller\ProductController::form");
$route->post("/produto/create", "\App\Controller\ProductController::save");

$route->get("/produto/{id}/update", "\App\Controller\ProductController::form");
$route->post("/produto/{id}/update", "\App\Controller\ProductController::update");

// ROTAS CATEGORIA //

$route->get("/categoria/list", "\App\Controller\CategoryController::list");

$route->get("/categoria/{id}/remove", "\App\Controller\CategoryController::remove");

$route->get("/categoria/create", "\App\Controller\CategoryController::form");
$route->post("/categoria/create", "\App\Controller\CategoryController::save");

$route->get("/categoria/{id}/update", "\App\Controller\CategoryController::form");
$route->post("/categoria/{id}/update", "\App\Controller\CategoryController::update");

// ROTA REGISTRO DE VENDAS //

$route->get("/venda", "\App\Controller\VendaController::index");
$route->post("/venda", "\App\Controller\VendaController::addCartItem");

$route->get("/venda/cart/{id}/remove", "\App\Controller\VendaController::deleteCartItem");
$route->get("/venda/cart/empty", "\App\Controller\VendaController::setCartEmpty");

$route->get("/venda/register", "\App\Controller\VendaController::vendaRegisterForm");
$route->post("/venda/register", "\App\Controller\VendaController::vendaRegisterSave");

$route->get("/venda/list", "\App\Controller\VendaController::vendaList");
$route->post("/venda/list", "\App\Controller\VendaController::findByDate");

$route->get("/venda/{id}/details", "\App\Controller\VendaController::getVendaDetails");


//echo "<pre>",var_dump($route->getRoutes()),"</pre>";

echo $route->dispatch();
