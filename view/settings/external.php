<?php
/**
 *
 */


$external_menu_feed = sprintf('https://%s/menu?%s'
	, $_SERVER['SERVER_NAME']
	, http_build_query([
		'c' => $_SESSION['Company']['id'],
		'o' => 'rss'
	])
);


$external_menu_link = sprintf('https://%s/menu?%s'
	, $_SERVER['SERVER_NAME']
	, http_build_query([
		'c' => $_SESSION['Company']['id'],
	])
);

$external_embed = sprintf('<script async defer src="https://%s/menu?c=%s&o=javascript"></script>'
	, $_SERVER['SERVER_NAME']
	, $_SESSION['Company']['id']
);

?>

<div class="container mt-4">


	<h2>External Menu:</h2>
	<div class="form-group">
		<div class="input-group">
			<input class="form-control form-control-lg" readonly type="text" value="<?= $external_menu_link ?>">
			<div class="input-group-append">
				<button class="btn btn-outline-secondary" type="button"><i class="fas fa-qrcode"></i></button>
			</div>
		</div>
	</div>


	<h2>JavaScript Embed:</h2>
	<div class="form-group">
		<input class="form-control form-control-lg" readonly value="<?= __h($external_embed) ?>">
	</div>


	<h2>External Inventory Feed:</h2>
	<div class="form-group">
		<div class="input-group">
			<input class="form-control form-control-lg" readonly type="text" value="<?= $external_menu_feed ?>">
			<div class="input-group-append">
				<button class="btn btn-outline-secondary" type="button"><i class="fas fa-qrcode"></i></button>
			</div>
		</div>
	</div>

<!--
	<h2>Another Thing:</h2>
	<div class="form-group">
		<div class="input-group">
			<input class="form-control form-control-lg" readonly type="text" value="<?= $delivery_auth_link ?>">
			<div class="input-group-append">
				<button class="btn btn-outline-secondary" type="button"><i class="fas fa-qrcode"></i></button>
			</div>
		</div>
	</div>
 -->

</div>
