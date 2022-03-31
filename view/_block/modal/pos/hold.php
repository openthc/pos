<?php
/**
 * Modal for Saving the Current Sale
 */


$body = <<<HTML
<div>
	<div class="mb-2">
	<label>Customer Name/Note</label>
		<input autocomplete="off" class="form-control" id="customer-name" value="">
	</div>
</div>
HTML;


$foot = <<<HTML
<button class="btn btn-outline-primary" id="pos-modal-sale-hold-save" name="a" type="button" value="save"><i class="fas fa-save"></i> Save</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-sale-hold',
	'modal_title' => 'Sales :: Create Hold',
	'body' => $body,
	'foot' => $foot,
]);
