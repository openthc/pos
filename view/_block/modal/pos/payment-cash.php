<?php
/**
 * The Payment Modal
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$body = <<<HTML
<div class="row">
	<div class="col-md-4">
		<h4>Sub-Total: $<span class="pos-checkout-sub" data-amount="0">-.--</span></h4>
	</div>
	<div class="col-md-4">
		<h4>Taxes: $<span class="pos-checkout-tax-total" data-amount="0">-.--</span></h4>
	</div>
	<div class="col-md-4">
		<h4 style="color:#f00000;">Total Due: $<span class="pos-checkout-sum"></span></h4>
		<!-- <input name="bill-due" type="hidden" value="<?=$due; ?>"> -->
	</div>
</div>

<div class="d-flex flex-wrap justify-content-between mb-2">
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="100">$100</button>
	</div>
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="50">$50</button>
	</div>
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="20">$20</button>
	</div>
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="10">$10</button>
	</div>
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="5">$5</button>
	</div>
	<div class="p-1 w-50">
		<button class="btn btn-lg btn-outline-secondary w-100 pp-cash" data-amount="1">$1</button>
	</div>
</div>

<div class="d-flex justify-content-between mb-2">
	<div class="p-1 w-25">
		<button class="btn btn-outline-secondary w-100 pp-cash" data-amount="0.25">0.25</button>
	</div>
	<div class="p-1 w-25">
		<button class="btn btn-outline-secondary w-100 pp-cash" data-amount="0.10">0.10</button>
	</div>
	<div class="p-1 w-25">
		<button class="btn btn-outline-secondary w-100 pp-cash" data-amount="0.05">0.05</button>
	</div>
	<div class="p-1 w-25">
		<button class="btn btn-outline-secondary w-100 pp-cash" data-amount="0.01">0.01</button>
	</div>
</div>


<div class="row">
	<div class="col-md-6">
		<div class="alert alert-secondary" id="amount-paid-wrap" style="font-size:28px; margin: 0;">
			<div class="d-flex justify-content-between">
				<div>Paid:</div>
				<div class="r" id="payment-cash-incoming">0.00</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="alert alert-warning" id="amount-need-wrap" style="font-size:28px; margin: 0;">
			<div class="d-flex justify-content-between">
				<div id="amount-need-hint">Due:</div>
				<div class="r" id="payment-cash-outgoing">0.00</div>
			</div>
		</div>
	</div>
</div>
HTML;


$foot = <<<HTML
<button class="btn btn-lg btn-warning" id="pos-pay-undo" type="button"><i class="fas fa-undo"></i> Undo</button>
<button class="btn btn-lg btn-primary" disabled id="pos-payment-commit" name="a" type="submit" value="pos-done">
	<i class="fas fa-check-square"></i> Complete
</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-payment',
	'modal_title' => 'Checkout :: Payment :: Cash',
	'body' => $body,
	'foot' => $foot,
]);
