<?php
/**
 * Modal for Adding a Discount
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$body = <<<HTML
<div class="row mb-4">
	<div class="col-md-8"><h5>Fixed Discount</h5></div>
	<div class="col-md-4">
		<div class="input-group">
			<input autocomplete="off" class="form-control r" id="pos-checkout-discount-fix" max="100" min="0" name="pos-discount-fixed" step="0.10" type="number" value="0.00">
			<div class="input-group-text"><i class="fas fa-money-bill-alt"></i></div>
		</div>
	</div>
</div>
<div class="row mb-4">
	<div class="col-md-8"><h5>Percent Discount</h5></div>
	<div class="col-md-4">
		<div class="input-group">
			<input autocomplete="off" class="form-control r" id="pos-checkout-discount-pct" max="100" min="0" name="pos-discount-percent" step="1" type="number" value="0">
			<div class="input-group-text"><i class="fas fa-percent"></i></div>
		</div>
	</div>
</div>

<div id="pos-modal-discount-list">
</div>


<div class="row mb-2">
	<div class="col-md-8"><h5>Balance:</h5></div>
	<div class="col-md-4">
		<h3 class="pos-checkout-sum" style="text-align:right;"></h3>
	</div>
</div>
<div class="row mb-2">
	<div class="col-md-8"><h5>Applied Discount</h5></div>
	<div class="col-md-4">
		<h3 class="pos-checkout-sum-adj" style="text-align:right;">-</h3>
	</div>
</div>
<div class="row mb-2">
	<div class="col-md-8"><h5>New Balance</h5></div>
	<div class="col-md-4">
		<h3 class="pos-checkout-sum-new" style="text-align:right;">-</h3>
	</div>
</div>

<!--

<div>
	<h2 style="background: #212121; color:#fcfcfc; margin:0; padding:4px;">Pre-Defined Discount Schedules</h2>
</div>

<div id="pos-modal-discount-list">
	<h3>Loading Discounts...</h3>
</div>
-->
HTML;


$foot = <<<HTML
<button class="btn btn-lg btn-outline-primary" data-bs-dismiss="modal" id="pos-discount-apply" type="button"><i class="fas fa-check-square"></i> Apply</button>
HTML;

echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-discount',
	'modal_title' => 'Checkout :: Discount',
	'body' => $body,
	'foot' => $foot,
]);
