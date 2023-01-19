<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10">

		<div class="col-12">
			<div><?= $message; ?></div>
			
			<form class="needs-validation" action="http://localhost/Penedo/produto/list" method="post" novalidate>
				<fieldset class="col-12">
					<legend class="h2  fw-light">Busca por Categoria</legend>

					<input class="form-control form-control-sm" id="categoria" type="text" name="categoria" placeholder="Digite o nome da Categoria" required>
					<div class="invalid-feedback">
						O campo categoria não pode ser vazio!
					</div>
				</fieldset>	
				<button  class="btn btn-primary mb-3" >Buscar</button>
			</form>
			<hr/>

			<h1 class="h2 fw-light">Lista de Produtos</h1>
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr class="table-dark text-center">
							<th colspan="2"></th>
							<th>
								Categoria
							</th>
							<th colspan="5">
								Produto
							</th>
						</tr>		
					</thead>
					<tbody>
						<tr class="table-secondary text-center">
							<th>Editar</th>
							<th>Remover</th>
							<th>Nome</th>
							<th>Código</th>
							<th>Descrição</th>
							<th>Preço de Custo</th>
							<th>Preço de Venda</th>
							<th>Estoque</th>
						</tr>
						
						<?php foreach($products as $product): ?>
							
						<tr class="text-center fw-light">
							<td><a href="/Penedo/produto/<?= $product->id ?? null; ?>/update">Editar</a></td>
							<td><a href="/Penedo/produto/<?= $product->id ?? null; ?>/remove">Remover</a></td>
							<td><?= $product->nome_categoria ?? null; ?></td>
							<td><?= $product->codigo ?? null; ?></td>
							<td><?= $product->descricao ?? null; ?></td>
							<td>R$ <?= $this->numberFormatBr($product->preco_custo) ?? null; ?></td>
							<td>R$ <?= $this->numberFormatBr($product->preco_venda) ?? null; ?></td>
							<td><?= $product->estoque ?? null; ?></td>
						</tr>
						<?php endforeach; ?>

					</tbody>
				</table>
			</div>
			<a  class="btn btn-success" href="/Penedo/produto/create">Cadastrar Produto</a>
		</div>
	</main>
