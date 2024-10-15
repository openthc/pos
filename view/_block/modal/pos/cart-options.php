<?php
/**
 * Modal for Cart Options
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$body = <<<HTML
<div class="row">
	<div class="col-md-6">
		<h5>Transaction Date:</h5>
		<p>Enter the Date to Register this Transaction</p>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="cart-option-date" type="date">
			<input autocomplete="off" class="form-control form-control-lg" id="cart-option-time" type="time">
		</div>
	</div>
</div>

HTML;

$foot = <<<HTML
<button class="btn btn-lg btn-primary"
	id="pos-cart-option-save"
	name="a"
	type="button"
	value="pos-cart-option-save"><i class="fas fa-check-square"></i> Apply
</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-cart-option',
	'modal_title' => 'Cart :: Options',
	'body' => $body,
	'foot' => $foot,
]);
