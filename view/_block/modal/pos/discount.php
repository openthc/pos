<?php
/**
 * Modal for Adding a Discount
 */

$body = <<<HTML
<div class="container">
<div class="row mb-4">
	<div class="col-md-8"><h5>Fixed Discount</h5></div>
	<div class="col-md-4">
		<div class="input-group">
			<input autocomplete="off" class="form-control" id="pos-checkout-discount-fix" max="100" min="0" name="pos-discount-fixed" step="0.10" type="number" value="0.00">
			<div class="input-group-append"><div class="input-group-text"><i class="fas fa-money-bill-alt"></i></div></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8"><h5>Percent Discount</h5></div>
	<div class="col-md-4">
		<div class="input-group">
			<input autocomplete="off" class="form-control" id="pos-checkout-discount-pct" max="100" min="0" name="pos-discount-percent" step="1" type="number" value="0">
			<div class="input-group-append"><div class="input-group-text"><i class="fas fa-percent"></i></div></div>
		</div>
	</div>
</div>
</div> <!-- /.container -->

<!--
<div>
	<h3 class="pos-checkout-sum" style="text-align:right;"></h3>
	<h3 class="pos-checkout-sum-adj" style="text-align:right;">-</h3>
	<h3 class="pos-checkout-sum-new" style="text-align:right;">-</h3>
</div>

<div>
	<h2 style="background: #212121; color:#fcfcfc; margin:0; padding:4px;">Pre-Defined Discount Schedules</h2>
</div>

<div id="pos-modal-discount-list">
	<h3>Loading Discounts...</h3>
</div>
-->
HTML;


$foot = <<<HTML
<button class="btn btn-outline-primary" id="pos-discount-apply" name="a" type="submit" value="pos-discount-apply"><i class="fas fa-check-square"></i> Apply</button>
HTML;

echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-discount',
	'modal_title' => 'Sales :: Discount',
	'body' => $body,
	'foot' => $foot,
]);
