<?php
/**
 * OpenTHC HTML POS Terminal Layout
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

header('content-type: text/html; charset=utf-8', true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="application-name" content="OpenTHC">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#247420">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha256-mUZM63G8m73Mcidfrv5E+Y61y7a12O5mW4ezU3bxqW4=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="https://cdn.openthc.com/bootstrap/5.1.3/bootstrap.min.css" integrity="sha256-YvdLHPgkqJ8DVUxjjnGVlMMJtNimJ6dYkowFFvp4kKs=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link href="/css/main.css" rel="stylesheet">
<!-- <link href="/css/pos.css" rel="stylesheet"> -->
<title><?= $data['Page']['title'] ?></title>
</head>
<body class="pos">
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
<div class="container-fluid">

	<a class="navbar-brand menu-left-toggle" type="button"><i class="fas fa-user-clock"></i></a>

	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu0" aria-controls="menu0" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="menu0">

		<div class="navbar-text mx-auto">
			<div style="color: #f9f9f9; font-family: monospace; font-size: 120%;"><?= $data['Page']['title'] ?></div>
		</div>

		<div class="navbar-text">
			<a class="btn btn-warning" href="/pos/open" id="pos-shop-redo"><i class="fas fa-ban" style="color: var(--bs-dark);"></i></a>
			<a class="btn btn-danger" href="/pos/shut"><i class="fas fa-power-off" style="color: var(--bs-dark);" ></i></a>
			<!-- <li class="nav-item"><a class="nav-link" href="/settings"><i class="fas fa-cogs"></i></a></li>
			<li class="nav-item"><a class="nav-link" href="/auth/shut"><i class="fas fa-power-off"></i></a></li> -->
		</div>

	</div>

</div>
</nav>

<!-- <div id="menu-zero">
	<div class="menu-item">
		<button class="btn btn-outline-secondary menu-left-toggle" type="button"><i class="fas fa-bars"></i></button>
	</div>
	<div class="menu-item">
		<div class="menu-item-text"><?= $data['Page']['title'] ?></div>
	</div>
	<div class="menu-item">
		<a class="btn btn-warning" href="/pos" id="pos-shop-redo" type="button"><i class="fas fa-ban"></i></a>
		<a class="btn btn-danger" href="/pos/shut"><i class="fas fa-power-off"></i></a>
	</div>
</div> -->
<?= $this->body ?>
<div class="shut" id="menu-left">
	<div class="menu-item">
		<button class="btn btn-outline-secondary menu-left-toggle" type="button"><i class="fas fa-bars"></i></button>
	</div>
	<div class="menu-item">
		<input class="form-control">
	</div>
	<div class="menu-item" id="sale-hold-list"></div>
</div>

<script src="https://cdn.openthc.com/lodash/4.17.15/lodash.js" integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.openthc.com/jquery/3.4.1/jquery.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.openthc.com/jqueryui/1.12.1/jqueryui.js" integrity="sha256-KM512VNnjElC30ehFwehXjx1YCHPiQkOPmqnrWtpccM=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.openthc.com/bootstrap/5.1.3/bootstrap.bundle.min.js" integrity="sha256-9SEPo+fwJFpMUet/KACSwO+Z/dKMReF9q4zFhU/fT9M=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/riot/4.14.0/riot.min.js" integrity="sha256-mxBp2pV/KfjX4uaj+6aEh2MWB7J+j8o6VuOCs4aY7zM=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- <script src="https://unpkg.com/@zxing/library@latest"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha256-xUHvBjJ4hahBW8qN9gceFBibSFUzbe9PNttUvehITzY=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/@zxing/library@0.19.2/umd/index.min.js" integrity="sha256-a0mo/OgjQ26D3n9JRYL4LMTeSx8PV3SYKv2My5wOdHE=" crossorigin="anonymous"></script>
<script src="/js/pos.js"></script>
<script src="/js/pos-scanner.js"></script>
<script src="/js/pos-printer.js"></script>
<script src="/js/pos-cart.js"></script>
<script src="/js/pos-modal-discount.js"></script>
<script src="/js/pos-modal-loyalty.js"></script>
<script src="/js/pos-modal-payment.js"></script>
<script src="/js/pos-camera.js"></script>
<script>
$(function () {
	$('.menu-left-toggle').on('click', function() {
		$m = $('#menu-left');
		if ($m.hasClass('open')) {
			$m.removeClass('open');
		} else {
			$m.addClass('open');
			window.document.dispatchEvent(new Event('menu-left-opened'));
		}
	});
});
</script>
<?= $this->foot_script ?>
</body>
</html>
