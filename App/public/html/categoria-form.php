<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10" >
		<div class="col-12"  >
			<?= $message; ?>
			<?php if($category->id ?? null): ?>
			<h1 class="h2 fw-light">Editar Categoria</h1>
			<?php else: ?>
			<h1 class="h2 fw-light">Cadastrar Categoria</h1>
			<?php endif; ?>
			<form class="needs-validation" action="" method="post" enctype="multipart/form-data" novalidate>					
				<input hidden readonly id="id" type="text" name="id" value = "<?= $category->id ?? null; ?>">
				<?php $readonly = (isset($category->id)) ? 'readonly': ''; ?> 

				<label class="form-label fw-light" for="codigo" >Código</label>
				<input class="form-control form-control-sm" id="codigo" type="text" name="codigo" 
				value = "<?= $category->codigo ?? null; ?>" <?= $readonly; ?> required>
				<div class="invalid-feedback">
					O campo 'código' não pode estar vazio!
				</div>


				<label class="form-label fw-light" for="descricao" >Nome</label>
				<input class="form-control form-control-sm"  id="descricao" type="text" name="nome" 
				value = "<?= $category->nome ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'nome' não pode estar vazio!
				</div>

				<button class="btn btn-primary mb-3">Enviar</button>
			</form>
			<a href="/Penedo/categoria/list" >Ir para Lista de Categorias</a>
		</div>
	</main>