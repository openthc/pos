<?php
/**
 * Delivery Manager
 */


$delivery_auth_link = sprintf('https://%s/intent?%s'
	, $_SERVER['SERVER_NAME']
	, http_build_query([
		'a' => 'delivery-auth',
		'c' => $_SESSION['Company']['id'],
		'l' => $_SESSION['License']['id']
	])
);


?>

<div class="container mt-4">

	<h2>Delivery Staff Sign In:</h2>
	<div class="mb-2">
		<div class="input-group">
			<input class="form-control form-control-lg" readonly type="text" value="<?= $delivery_auth_link ?>">
			<button class="btn btn-outline-secondary qrcode-link" data-code="<?= $delivery_auth_link ?>" type="button"><i class="fas fa-qrcode"></i></button>
		</div>
	</div>

<!--
	<h2>Another Thing:</h2>
	<div class="mb-2">
		<div class="input-group">
			<input class="form-control form-control-lg" readonly type="text" value="<?= $delivery_auth_link ?>">
			<button class="btn btn-outline-secondary" type="button"><i class="fas fa-qrcode"></i></button>
		</div>
	</div>
 -->

</div>
