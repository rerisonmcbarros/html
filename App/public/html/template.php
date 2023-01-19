<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $title ?? null; ?></title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>
	<style type="text/css">
	
		#nav li:hover{

			background-color: #333;
		}
		.form-label{
			margin-top: 0;
			margin-bottom: 0;
		}
		.form-control{
			margin-top: 0;
			margin-bottom: 1em;
		}
		.invalid-feedback{
			margin-top: -1em;
			margin-bottom: 1em;
		}
		.bi{

			margin-right: 1em;
			font-size: 1.5em;
		}

		@media (max-width:1024px){

			header {
				
				width: 56px;
			}

			header a, hr{

				display: none;
			}

			main.col-10{

				width: 94%;
				clear: both;
			}
		}

		

	</style>
</head>
<body>
	<header class="d-inline-block float-start position-fixed col-xl-2" style="transition: none;">
		<div class="d-flex flex-column text-bg-dark overflow-hidden" style="height: 100vh;">
			<a class="text-decoration-none ps-3 pt-3" href="/Penedo"><p class="h1 fw-light">Penedo</p></a>
			<hr/>
			<ul id="nav" class="nav flex-column">
				<li class="nav-item ps-3"><a class="nav-link text-white ps-0 m-0 text-nowrap" title="Home" href="/Penedo"><i class="bi bi-house"></i>Home</a></li>
				<li class="nav-item ps-3"><a class="nav-link text-white ps-0 m-0 text-nowrap" title="Categorias" href="/Penedo/categoria/list"><i class="bi bi-tags"></i>Categorias</a></li>
				<li class="nav-item ps-3"><a class="nav-link text-white ps-0 m-0 text-nowrap" title="Produtos" href="/Penedo/produto/list"><i class="bi bi-box2"></i>Produtos</a></li>
				<li class="nav-item ps-3"><a class="nav-link text-white ps-0 m-0 text-nowrap" title="Vendas" href="/Penedo/venda/list"><i class="bi bi-card-checklist"></i>Vendas</a></li>
				<li class="nav-item ps-3"><a class="nav-link text-white ps-0 m-0 text-nowrap" title="Registrar Venda" href="/Penedo/venda"><i class="bi bi-bag"></i>Registrar Venda</a></li>
			</ul>
		</div>
	</header>


	<?= $this->section('content'); ?>

	<script src="/Penedo/App/public/js/Page.js" type="text/javascript"></script>
	<script src="/Penedo/App/public/js/Controller.js" type="text/javascript"></script>
</body>
</html>