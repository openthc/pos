<?php
/**
 * The Payment Modal
 */

$body = <<<HTML
<div class="container">
<div class="row">
	<div class="col-md-4">
		<h4>Sub-Total: $<span class="pos-checkout-sub" data-amount="0">-.--</span></h4>
	</div>
	<!--
	<div class="pure-u-1-4" style="text-align:center;">
		<h4>I-502 Tax: $<span class="pos-checkout-tax-i502" data-amount="<?=$tax_i502?>"><?=number_format($tax_i502, 2)?></span></h4>
	</div>
	-->
	<div class="col-md-4">
		<h4>Sales Tax: $<span class="pos-checkout-tax-sale" data-amount="0">-.--</span></h4>
	</div>
	<div class="col-md-4">
		<h4 style="color:#f00000;">Total Due: $<span class="pos-checkout-sum"></span></h4>
		<!-- <input name="bill-due" type="hidden" value="<?=$due; ?>"> -->
	</div>
</div>

<div class="row" style="padding-top: 16px;">
<div class="col-md-12">
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-6"><div style="margin:0 4px 4px 0;"><button class="btn btn-outline-secondary btn-block pp-card" style="font-size:48px; width:100%;"><i class="fab fa-cc-mastercard"></i></button></div></div>
			<div class="col-md-6"><div style="margin:0 4px 4px 0;"><button class="btn btn-outline-secondary btn-block pp-card" style="font-size:48px; width:100%;"><i class="fab fa-cc-visa"></i></button></div></div>
		</div>
		<div class="row">
			<div class="col-md-6"><div style="margin:0 4px 4px 0;"><button class="btn btn-outline-secondary btn-block pp-card" style="font-size:48px; width:100%;"><i class="fab fa-cc-amex"></i></button></div></div>
			<div class="col-md-6"><div style="margin:0 4px 4px 0;"><button class="btn btn-outline-secondary btn-block pp-card" style="font-size:48px; width:100%;"><i class="fab fa-cc-discover"></i></button></div></div>
		</div>
	</div>
</div>
</div> <!-- /.row -->

<div class="row" style="padding-top: 16px;">
	<div class="col-md-6" style="text-align:center;">
		<span style="font-size:32px;">Paid:</span>
		<div style="padding: 4px 8px;">
			<input autocomplete="off" class="form-control psi-item-size" id="pp-card-pay" name="payment-made" step="0.01" style="width: 98%;" type="number" value="0.00">
		</div>
	</div>
	<div class="col-md-6" style="text-align:center;">
		<span style="font-size:32px;">Due:</span>
		<div style="padding: 4px 8px;">
			<input autocomplete="off" class="form-control psi-item-size" id="payment-need" name="payment-need" step="0.01" style="width: 98%;" type="number" value="0.00">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h2 style="font-size:32px; text-align:center;">Change: $<span id="pos-back-due">0.00</span></h2>
	</div>
</div> <!-- /.row -->
</div> <!-- /.container -->
HTML;


$foot = <<<HTML
<button class="btn btn-lg btn-warning" data-bs-dismiss="modal" id="pos-pay-undo" style="display: none;" type="button"><i class="fas fa-undo"></i> Undo</button>
<button class="btn btn-lg btn-primary" disabled id="pos-payment-commit" name="a" type="submit" value="pos-done"><i class="fas fa-check-square-o"></i> Complete</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-payment',
	'modal_title' => 'Collect Payment',
	'body' => $body,
	'foot' => $foot,
]);
