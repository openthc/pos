<?php
/**
 *
 */

?>

<div id="card-modal" style="display:none;">

<h2 style="background: #212121; color:#fcfcfc; margin:0; padding:4px;">Swipe Card</h2>
<div style="margin:8px;">
	<div class="row">
		<div class="col-md-6" style="text-align:center;">
			<div style="padding:4px;"><input class="psi-item-size" placeholder="Transaction" type="text" style="width:100%;"></div>
		</div>
		<div class="col-md-6" style="text-align:right;">
			<div style="padding:4px;"><input class="psi-item-size" id="pp-card-confirm" placeholder="Amount" style="width: 6em;" type="number" min="0" step="0.01"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6" style="text-align:center;"><button class="warn" id="pos-card-back" name="a" style="margin: 8px auto; width:80%;" type="button"><i class="fas fa-times"></i> Cancel</button></div>
		<div class="col-md-6" style="text-align:center;"><button class="good" id="pos-card-done" name="a" style="margin: 8px auto; width:80%;" type="submit"><i class="fas fa-check-square-o"></i> Complete</button></div>
	</div>
</div>

</div>

<script>
$(function() {

	// Close when Cancel is clicked
	$('#pos-card-back').on('click', function() {
		$('#pos-modal-checkout-card-swipe').modal('shut');
	});

});
</script>

echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-checkout-card-swipe',
	'modal_title' => 'Scan ID',
	'body' => $body,
	'foot' => $foot,
]);
