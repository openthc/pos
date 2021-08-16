<?php
/**
 *
 */

?>

<div id="pos-modal-transaction-limit" style="display:none;">
	<div class="warn">
		<h2>Transaction Over Limits</h2>
	</div>

	<div style="margin:0 32px;">
		<h3>According to <a href="http://app.leg.wa.gov/WAC/default.aspx?cite=314-55-095" target="_blank">WAC 314-55-095</a> there are transaction limitations.</h3>
		<ul>
		<li>One ounce (1oz, 28g) of usable marijuana</li>
		<li>Sixteen ounces (16oz, 453g) of marijuana-infused product in solid form</li>
		<li>Seventy-two ounces (72 floz, ~2L) of marijuana-infused product in liquid form</li>
		<li>Seven grams (7g) of marijuana-infused extract for inhalation</li>
		</ul>
	</div>

	<div class="row" id="pos-foot" style="position:relative;">
		<div class="col-md-6" style="text-align:center;">
			<button class="btn btn-outline-warning" id="pos-ticket-reset" type="button"> Reset</button>
		</div>
		<div class="col-md-6" style="text-align:center;">
			<button class="btn btn-outline-success" name="a" type="button" value="pos-discount-apply">OK</button>
		</div>
	</div>

</div>


<script>
$(function() {
	$('#pos-ticket-reset').on('click', function() {
		Weed.modal('shut');
		// Blank the Ticket
		e.preventDefault()
		e.stopPropagation();
		return false;
	});

	$('#pos-modal-transaction-limit button.good').on('click', function() {
		Weed.modal('shut');
		e.preventDefault()
		e.stopPropagation();
		return false;
	});

});
</script>
