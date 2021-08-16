<header class="body-head bg-dark">
	<nav>
		<div class="item logo">
			<div style="font-size: 32px;">
				<a class="btn btn-sm" href="/dashboard"><img alt="OpenTHC Icon" src="https://cdn.openthc.com/img/icon/icon-w-32.png"></a>
			</div>
		</div>
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
	<div id="action-panel" style="background: #f0f0f0f0; border: 2px solid #333; display: none; margin: 0; padding: 0.50rem; position: absolute;">
			<h2>Some Action Panel Here?</h2>
	</div>
</header>
