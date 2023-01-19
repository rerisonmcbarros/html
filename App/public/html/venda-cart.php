<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10">

		<div class="col-12">
			<?= $message; ?>

			<form class="needs-validation" action="http://localhost/Penedo/venda" method="post" novalidate>

				<fieldset class="col-12">
					<legend class="h2 fw-light">Adicionar Produto ao Carrinho</legend>

					<input class="form-control form-control-sm " id="codigo" type="text" name="codigo" placeholder="Digite o codigo do Produto" required>
					<div class="invalid-feedback">
						"O campo código do produto não pode ser vazio!"
					</div>

					<input class="form-control form-control-sm" id="quantidade" type="text" name="quantidade" placeholder="Digite a quantidade" required>
					<div class="invalid-feedback">
						"O campo quantidade não pode ser vazio!"
					</div>
				</fieldset>

				<button class="btn btn-primary mb-3">Adicionar ao Carrinho</button>
			</form>

			<hr/>

			<h1 class="h2 fw-light">Carrinho de Compras</h1>
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr class="table-dark text-start">	
							<th colspan="5">
								Lista de Produtos
							</th>
						</tr>		
					</thead>
					<tbody>
						<tr class="table-secondary text-center">
							<th class="col-1">Remover</th>
							<th class="col-1">Código</th>
							<th>Descrição</th>
							<th class="col-1">Quantidade</th>
							<th>Valor</th>
						</tr>
						
						<?php foreach(($cart->getCartItems()) as $item): ?>
						
						<?php $product = ($item->getProduct() ?? null); ?>

						<tr class="text-center fw-light">	
							<td><a href="/Penedo/venda/cart/<?= $product->id ?? null; ?>/remove">Remover</a></td>
							<td><?= $product->codigo ?? null; ?></td>
							<td><?= $product->descricao ?? null; ?></td>
							<td><?= $item->getQuantity() ?? null; ?></td>
							<td>R$ <?= $this->numberFormatBr($item->getSubTotal()) ?? null; ?></td>
						</tr>
						<?php endforeach; ?>

						<tr>
							<th class="text-end" colspan="4"> Valor Total:</th>
							<th class="text-center" >R$ <?= $this->numberFormatBr($cart->getTotal()) ?? "0,00";  ?></th>
						</tr>

					</tbody>
				</table>
			</div>
			<a class="btn btn-success" href="/Penedo/venda/register" target="_self">Finalizar Venda</a>
			<a class="btn btn-warning" href="/Penedo/venda/cart/empty" target="_self">Esvaziar Carrinho</a>
			
		</div>
	</main>
