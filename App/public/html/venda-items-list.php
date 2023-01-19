<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10">

		<div class="col-12">
			<?= $message; ?>

			<h1 class="h2 fw-light">Detalhes da Venda</h1>
			<hr/>
			<p><span class="fw-bold">Dados do Cliente:</span> <?= ($sale->nome_cliente ?? null); ?></p>
			<p><span class="fw-bold">Forma de Pagamento:</span> <?= ($sale->pagamento ?? null); ?></p>
			<p><span class="fw-bold">Data da Venda:</span> <?= ($this->dateFormatBr($sale->data_venda) ?? null); ?></p>
			
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr class="table-dark text-start">
							<th colspan="6">
							 Itens da Venda
							</th>
						</tr>		
					</thead>
					<tbody>
						<tr class="table-secondary text-center">
							<th>Código</th>
							<th>Descrição</th>
							<th>Quantidade</th>
							<th>Preço Unitário</th>
						</tr>
						
						<?php foreach(($itemsSale ?? []) as $item): ?>
							
						<tr class="text-center fw-light">
							<td><?= $item->codigo ?? null; ?></td>
							<td><?= $item->descricao ?? null; ?></td>
							<td><?= $item->quantidade ?? null; ?></td>
							<td>R$ <?= $this->numberFormatBr($item->item_preco) ?? null; ?></td>
						</tr>
						<?php endforeach; ?>
						<tr>
							<th class="text-end" colspan="3">Valor Total:</th>
							<th class="text-center">R$ <?= $this->numberFormatBr($totalValue) ?? null; ?></th>				
						</tr>
						<tr>
							<th class="text-end" colspan="3">Desconto:</th>
							<th class="text-center"> <?= $sale->desconto ?? null; ?>%</th>				
						</tr>
						<tr>
							<th class="text-end" colspan="3">Valor Final:</th>
							<th class="text-center">R$ <?= $this->numberFormatBr($sale->valor_total) ?? null; ?></th>				
						</tr>

					</tbody>
				</table>
				<a href="/Penedo/venda/list">Voltar para a Lista de Vendas</a>
			</div>
		</div>
	</main>
