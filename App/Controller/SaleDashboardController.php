<?php

namespace App\Controller;

use \Exception;
use App\View\Engine;
use Lib\Database\Transaction;
use App\Repository\SaleRepository;

class SaleDashboardController extends Controller
{
    public function index()
    {
        try{
            Transaction::open(DB_CONFIG);

            $get = filter_var_array($this->request->get(), FILTER_SANITIZE_SPECIAL_CHARS);

            $saleRepository = new SaleRepository();
            $years = $saleRepository->getSaleYears();

            $selectedYear = $get['year'] ?? date('Y');

            $dataSaleQuantityChart = $this->getDataSaleQuantityChart($selectedYear);
            $dataSaleValueChart = $this->getDataSaleValueChart($selectedYear);

            Transaction::close();

        } catch (Exception $e) {

            $labels = [
                'Janeiro', 
                'Fevereiro', 
                'Março', 
                'Abril', 
                'Maio', 
                'Junho', 
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro'
            ];

            $data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            $dataSaleQuantityChart = json_encode([
                'labels' => $labels,
                'sale_quantity' => $data
            ]);
            $dataSaleValueChart = json_encode([
                'labels' => $labels,
                'total_value_month' => $data
            ]);

            echo "<pre>", var_dump($dataSaleQuantityChart), "</pre>";
        }	
        
        $engine = new Engine(__DIR__."/../../App/public/html/");

        echo $engine->render(
            "venda-dashboard", 
            [
                'title' => 'Penedo | Painel de Vendas',
                'selectedYear' => $selectedYear ?? null,
                'years' => ($years ?? [])
            ],
            [
                'quantity-chart' => $dataSaleQuantityChart,
                'value-chart' => $dataSaleValueChart
            ]
        );
    }

    public function getDataSaleQuantityChart($year = null){

        try {
            Transaction::open(DB_CONFIG);

            $saleRepository = new SaleRepository();

            $labels = [
                'Janeiro', 
                'Fevereiro', 
                'Março', 
                'Abril', 
                'Maio', 
                'Junho', 
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro'
            ];

            $quantityByMonth = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            foreach ($saleRepository->getQuantityGroupByMonth($year) as $item) {
                $quantityByMonth[$item['month']-1] = $item['sale_quantity'];
            }
    
            return json_encode([
                'labels' => $labels,
                'sale_quantity' => $quantityByMonth
            ]);

            Transaction::close();
        } catch (Exception $e) {

            throw $e;
        }
    }

    public function getDataSaleValueChart($year = null){

        try {
            Transaction::open(DB_CONFIG);

            $saleRepository = new SaleRepository();

            $labels = [
                'Janeiro', 
                'Fevereiro', 
                'Março', 
                'Abril', 
                'Maio', 
                'Junho', 
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro'
            ];

            $totalValueByMonth = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            foreach ($saleRepository->getTotalValueGroupByMonth($year) as $item) {
                $totalValueByMonth[$item['month']-1] = number_format($item['total_value_month'], 2, '.', '');
            }
    
            return json_encode([
                'labels' => $labels,
                'total_value_month' => $totalValueByMonth
            ]);

            Transaction::close();
        } catch (Exception $e) {

            throw $e;
        }
    }
}
