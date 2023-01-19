<?php $this->layout('template'); ?>

	<main class="d-inline-block float-end p-5 col-10">

		<div class="col-12">
			<?= $message; ?>
			<h1 class="h2 fw-light">Registrar Venda</h1>
			<form class="needs-validation" action="" method="post" enctype="multipart/form-data" novalidate>
				

				<label class="form-label">Dados do Cliente</label>
				<input class="form-control form-control-sm" type="text" id="nome_cliente" name="nome_cliente" required>
				<div class="invalid-feedback">
					"O campo 'Nome do Cliente' não pode ser vazio!"
				</div>		

				<label class="form-label">Valor Total</label>
				<input readonly class="form-control form-control-sm" type="text" id="valor_total" name="valor_total" value="<?= $this->numberFormatBr(($cart->getTotal()) ?? '0.00'); ?>" required>
			
				<label class="form-label">Desconto</label>
				<input class="form-control form-control-sm" type="text" id="desconto" name="desconto" required>
				<div class="invalid-feedback">
					"O campo 'Desconto' não pode ser vazio!"
				</div>	

				<label class="form-label">Pagamento</label>
				<input class="form-control form-control-sm" type="text" id="pagamento" name="pagamento" required>
				<div class="invalid-feedback">
					"O campo 'Pagamento' não pode ser vazio!"
				</div>			

				<button class="btn btn-primary mb-3 " >Enviar</button>
			</form>
			<a href="/Penedo/venda">Retornar para Carrinho de Compras</a>
		</div>
	</main>
