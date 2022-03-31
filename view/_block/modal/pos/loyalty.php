<?php
/**
 * Modal for Loyalty Program
 * @todo Maybe just one input box for any/all codes?
 */

$body = <<<HTML
<div class="row mb-4">
	<div class="col-md-6">
		<h5>Phone</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="loyalty-phone" inputmode="tel" type="phone">
			<div class="input-group-text"><i class="fas fa-hashtag"></i></div>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col-md-6">
		<h5>Email</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="loyalty-email" inputmode="email" type="email">
			<div class="input-group-text"><i class="fas fa-at"></i></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<h5>Code / ID</h5>
		<p>Their Loyalty ID or scanned ID card</p>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="loyalty-other" type="text">
			<button class="btn btn-secondary" type="button"><i class="fas fa-qrcode"></i></button>
		</div>
	</div>
</div>
HTML;

$foot = <<<HTML
<button class="btn btn-primary" id="pos-loyalty-apply" name="a" type="submit" value="pos-loyalty-apply"><i class="fas fa-check-square"></i> Apply</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-loyalty',
	'modal_title' => 'Sales :: Loyalty',
	'body' => $body,
	'foot' => $foot,
]);
