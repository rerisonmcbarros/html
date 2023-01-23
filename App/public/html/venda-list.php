<?php $this->layout('template'); ?>

	<main  class="d-inline-block float-end p-5 col-10">
		<div class="col-12 ">
			
			<?= $message; ?>
			
			<form class="needs-validation" action="http://localhost/Penedo/venda/list" method="post" novalidate>

				<fieldset class="col-12">
					<legend class="h2 fw-light">Buscar por Período</legend>

					<label class="form-label" for="data_inicio">Data início</label>
					<input class="form-control form-control-sm" id="data_inicio" type="date" name="data_inicial" required>
					<div class="invalid-feedback">
						O campo data início não pode ser vazio!
					</div>
					

					<label class="form-label" for="data_final">Data final</label>
					<input class="form-control form-control-sm" id="data_final" type="date" name="data_final" required>
					<div class="invalid-feedback">
						O campo data início não pode ser vazio!
					</div>

				</fieldset>
				
				<button  class="btn btn-primary" >Buscar</button>
			</form>
			<hr/>

			<h1 class="h2 fw-light">Lista de  Vendas</h1>
			<div class="table-responsive">
				<table class="table table-bordered  table-hover">
					<thead>
						<tr class="table-dark text-start">
							<th colspan="6">
							 Vendas
							</th>
						</tr>		
					</thead>
					<tbody>
						<tr class="table-secondary text-center">
							<th>Nome do Cliente</th>
							<th>Desconto</th>
							<th>Valor Total</th>
							<th>Forma de Pagamento</th>
							<th>Data</th>
							<th>Detalhes</th>
						</tr>
						<?php foreach(($sales ?? []) as $sale): ?>
							
						<tr class="text-center fw-light">
							<td><?= $sale->nome_cliente ?? null; ?></td>
							<td><?= $sale->desconto ?? null; ?></td>
							<td>R$ <?= $this->numberFormatBr($sale->valor_total) ?? null; ?></td>
							<td><?= $sale->pagamento ?? null; ?></td>
							<td><?= $this->dateFormatBr($sale->data_venda) ?? null; ?></td>
							<td><a href="/Penedo/venda/<?= $sale->id ; ?>/details">Ver detalhes</a></td>
						</tr>
						<?php endforeach; ?>

						<?php if($valorPeriodo): ?>
						<tr>
							<th class="text-end" colspan="5">Valor Total:</th>
							<th class="text-center">R$ <?= $this->numberFormatBr($valorPeriodo); ?></th>
						</tr>
						<?php endif; ?>

					</tbody>
				</table>
				<div class="pagination  justify-content-center">
					<?= ($links ?? null); ?>
				</div>
			</div>	
		</div>
	</main>