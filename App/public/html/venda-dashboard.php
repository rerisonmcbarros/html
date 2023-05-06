<?php $this->layout('template') ?>;

    <main class="d-inline-block float-end p-5 col-10">
        <section>
            <h1 class="h2 fw-light mb-3">Painel de Vendas</h1>
            
            <form class="needs-validation" action="/Penedo/venda/dashboard" method="get" novalidate>		
                <label class="form-label fw-light" for="year" >Ano</label>
                <select class="form-select form-select-sm mb-3" name="year" id="year" required> 
                    <?php foreach ($years  as $item): ?>
                    <?php $check =  $selectedYear == $item['year'] ? 'selected=1' : ''; ?>

                    <option <?= $check; ?> value="<?= $item['year'] ?? null; ?>"> <?= $item['year'] ?? null; ?> </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary mb-3">Buscar</button>
            </form>
        
            <hr>
            <div>
                <h1 class="h4 fw-light">Quantidade de Vendas Mensal</h1>
                <canvas id="sale-quantity-chart"></canvas>
            </div>
            <hr class="my-5">
            <div>
                <h1 class="h4 fw-light">Valor Total de Vendas Mensal</h1>
                <canvas id="sale-value-chart"></canvas>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        let dataSaleQuantityChart = {{quantity-chart}};
        let dataSaleTotalValueChart ={{value-chart}};
        
        function getChartSaleQuantity() {
            
            const ctx = document.getElementById('sale-quantity-chart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataSaleQuantityChart.labels,
                    datasets: [{
                    label: 'Quantidade de Vendas',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    data: dataSaleQuantityChart.sale_quantity,
                    borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                    y: {
                        beginAtZero: true
                    }
                    }
                }
            });
          
        }

        function getChartSaleValue() {
                  
            const ctx = document.getElementById('sale-value-chart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataSaleTotalValueChart.labels ?? [],
                    datasets: [{
                    label: 'Valor Total de Vendas',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: dataSaleTotalValueChart.total_value_month,
                    borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                    y: {
                        beginAtZero: true
                    }
                    }
                }
            });
        }
    
        getChartSaleQuantity();
        getChartSaleValue();
            
    </script>
