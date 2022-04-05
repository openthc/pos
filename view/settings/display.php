<?php
/**
 * Digital Display Manager
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$link0 = sprintf('https://%s/pub/display/%s?m=cast', $_SERVER['SERVER_NAME'], _ulid());
$link1 = sprintf('https://%s/pub/display/%s?m=fire', $_SERVER['SERVER_NAME'], _ulid());

?>

<div class="container mt-4">

<div class="hero">
	<h1>Internal Display</h1>
	<p>Configure TV or other digital media displays</p>
</div>

<div class="mb-4">
	<label>Chrome Cast</label>
	<div class="input-group">
		<input class="form-control" value="<?= $link0 ?>">
		<button class="btn btn-secondary qrcode-link" data-code="<?= $link0 ?>" type="button"><i class="fas fa-qrcode"></i></button>
	</div>
</div>

<div class="mb-2">
	<label>FireTV</label>
	<div class="input-group">
		<input class="form-control" value="<?= $link1 ?>">
		<button class="btn btn-secondary qrcode-link" data-code="<?= $link1 ?>" type="button"><i class="fas fa-qrcode"></i></button>
	</div>
</div>

</div>
