<?php $this->layout('template'); ?>
	
	<main class="d-inline-block float-end p-5 col-10">
		<div class="col-12" >
			<?= $message; ?>
			<h1 class="h2 fw-light">Lista de Categorias</h1>
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr class="table-dark">
							<th colspan="4" >Categorias</th>
						</tr>
						<tr class="table-secondary text-center">
							<th class="col-1">Editar</th>
							<th class="col-1">Remover</th>
							<th class="col-2">Código</th>
							<th>Nome</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach(($categories ?? []) as $category): ?>	
						<tr class="fw-light text-center">
							<td><a href="/Penedo/categoria/<?= $category->id ?? null; ?>/update">Editar</a></td>
							<td><a href="/Penedo/categoria/<?= $category->id ?? null; ?>/remove">Remover</a></td>
							<td><?= $category->codigo ?? null; ?></td>
							<td><?= $category->nome ?? null; ?></td>		
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<a class="btn btn-success" href="/Penedo/categoria/create">Cadastrar Categoria</a>
		</div>
	</main>
	<?php $this->start('style'); ?>

		<link rel="stylesheet" type="text/css" href="#">

	<?php $this->stop(); ?>



