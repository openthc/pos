<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
	<div class="item logo">
		<div style="font-size: 32px;">
			<a class="btn btn-sm" href="/dashboard"><img alt="OpenTHC Icon" src="https://cdn.openthc.com/img/icon/icon-w-32.png"></a>
		</div>
	</div>

	<ul class="navbar-nav mr-auto">
		<li class="nav-item"><a class="nav-link" href="/pos"><i class="fas fa-cash-register"></i> POS</a></li>
		<li class="nav-item"><a class="nav-link" href="/crm"><i class="fas fa-users"></i> CRM</a></li>
	</ul>

	<div class="item find">
		<form action="/search" autocomplete="off" class="form-inline" id="search-form">
			<div class="input-group">
				<div class="input-group-prepend">
					<button class="btn btn-outline-secondary" type="button" id="scanner-search-ready" style="color: rgb(119, 119, 119);"><i class="fas fa-barcode"></i></button>
				</div>
				<input autocomplete="off" class="form-control" id="search-q" name="q" placeholder="Search" title="Search (use '/' to focus)" type="text">
				<div class="input-group-append">
					<button class="btn btn-outline-success"><i class="fas fa-search"></i></button>
				</div>
			</div>
		</form>
	</div>
	<div class="item tool navbar-expand-md">
		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="/auth/shut"><i class="fas fa-power-off"></i></a></li>
		</ul>
	</div>
</nav>
