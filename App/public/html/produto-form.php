<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10">

		<div class="col-12">
			<div><?= $message; ?></div>
			<?php if($product->id ?? null): ?>
			<h1 class="h2 fw-light">Editar Produto</h1>
			<?php else: ?>
			<h1 class="h2 fw-light">Cadastrar Produto</h1>
			<?php endif; ?>

			<form class="needs-validation" action="" method="post" enctype="multipart/form-data" novalidate>		
				<input hidden id="id" type="text" name="id" value = "<?= $product->id ?? null; ?>" >

				<label class="form-label fw-light" for="codigo" >Código</label>
				<?php $readonly = isset($product->id) ? 'readonly' : ''; ?>
				<input class="form-control form-control-sm" <?= $readonly ?> id="codigo" type="text" name="codigo" value = "<?= $product->codigo ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'código' não pode estar vazio!
				</div>
				
				<label class="form-label fw-light" for="categoria" >Categoria</label>
				<select class="form-control form-control-sm" name="id_categoria" id="categoria" required>
				 	<option value=""></option>
				 	
					<?php foreach (($categories ?? []) as $category): ?>
					<?php $check = (($product->id_categoria ?? null) == ($category->id)) ? 'selected=1' : ''; ?>

					<option <?= $check; ?> value="<?= $category->id ?? null; ?>"> <?= $category->nome ?? null; ?> </option>
					<?php endforeach; ?>
				
				</select>
				<div class="invalid-feedback">
					O campo 'categoria' não pode estar vazio!
				</div>

				<label class="form-label fw-light" for="descricao" >Descricao</label>
				<input class="form-control form-control-sm" id="descricao" type="text" name="descricao" value = "<?= $product->descricao ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'descrição' não pode estar vazio!
				</div>

				<label class="form-label fw-light" for="preco_custo" >Preço de Custo</label>
				<input class="form-control form-control-sm" id="preco_custo" type="text" name="preco_custo" value = "<?= $product->preco_custo ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'preço de custo' não pode estar vazio!
				</div>

				<label class="form-label fw-light" for="preco_venda" >Preço de Venda</label>
				<input class="form-control form-control-sm" id="preco_venda" type="text" name="preco_venda" value = "<?= $product->preco_venda ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'preço de venda' não pode estar vazio!
				</div>

				<label class="form-label fw-light" for="estoque" >Estoque</label>
				<input class="form-control form-control-sm" id="estoque" type="text" name="estoque" value = "<?= $product->estoque ?? null; ?>" required>
				<div class="invalid-feedback">
					O campo 'estoque' não pode estar vazio!
				</div>

				<!-- UPLOAD DE IMAGENS -->
				<label hidden class="form-label fw-light" for="file" >Imagens</label>
				<input  hidden class="form-control form-control-sm" id="file" type="file" name="file[]"  multiple>

				<button class="btn btn-primary mb-3" >Enviar</button>
			</form>
			<a class="" href="/Penedo/produto/list" >Ir para Lista de Produtos</a>
		</div>
	</main>
		

