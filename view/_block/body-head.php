<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
<div class="container-fluid">

	<a class="navbar-brand" href="/dashboard"><img alt="OpenTHC Icon" src="https://cdn.openthc.com/img/icon/icon-w-32.png"></a>

	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu0" aria-controls="menu0" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="menu0">

		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="/pos"><i class="fas fa-cash-register"></i> POS</a></li>
			<li class="nav-item"><a class="nav-link" href="/crm"><i class="fas fa-users"></i> CRM</a></li>
		</ul>

		<form action="/search" autocomplete="off" class="d-flex" id="search-form">
			<div class="input-group">
				<button class="btn btn-outline-secondary" type="button" id="scanner-search-ready" style="color: rgb(119, 119, 119);"><i class="fas fa-barcode"></i></button>
				<input autocomplete="off" class="form-control" id="search-q" name="q" placeholder="Search" title="Search (use '/' to focus)" type="text">
				<button class="btn btn-outline-success"><i class="fas fa-search"></i></button>
			</div>
		</form>

		<ul class="navbar-nav ms-auto">
			<li class="nav-item"><a class="nav-link" href="/settings"><i class="fas fa-cogs"></i></a></li>
			<li class="nav-item"><a class="nav-link" href="/auth/shut"><i class="fas fa-power-off"></i></a></li>
		</ul>
	</div>

</div>
</nav>
