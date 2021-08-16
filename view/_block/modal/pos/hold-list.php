<?php
/**
 * Modal for Saving the Current Sale
 */

$body = <<<HTML
<div id="sale-hold-list-wrap">
	<h2><i class="fas fa-sync fa-spin"></i> Loading ...</h2>
</div>
HTML;


$foot = <<<HTML
<button class="btn btn-outline-primary" id="pos-modal-sale-hold-save" name="a" type="button" value="save"><i class="fas fa-save"></i> Save</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-sale-hold-list',
	'modal_title' => 'Sales :: Hold',
	'body' => $body,
	'foot' => $foot,
]);
