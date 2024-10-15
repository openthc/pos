<?php
/**
 * Customer Information Modal
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */


$_input_group = function($pre_text, $name_id, $hint, $val)
{
	ob_start();
?>
<div class="input-group input-group-lg mb-4">
<div class="input-group-text"><?= $pre_text ?></div>
<input autocomplete="off"
	class="form-control form-control-lg"
	id="<?= $name_id ?>"
	name="<?= $name_id ?>"
	placeholder="<?= $hint ?>"
	type="text"
	value="<?= __h($val) ?>">
</div>
<?php
	return ob_get_clean();
};


ob_start();

echo $_input_group('Name', '', '', $_SESSION['Cart']['Contact']['fullname']);
echo $_input_group('Patient ID', '', '', $_SESSION['Cart']['Contact']['guid']);
echo $_input_group('Code', '', '', $_SESSION['Cart']['Contact']['code']);
echo $_input_group('Phone', 'pos-client-contact-phone', '', $_SESSION['Cart']['Contact']['phone']);
echo $_input_group('Email', 'pos-client-contact-email', '', $_SESSION['Cart']['Contact']['email']);

$body = ob_get_clean();

$foot = <<<HTML
<button class="btn btn-lg btn-primary" id="pos-client-contact-update" name="a" type="submit" value="pos-client-contact-update">
	<i class="fas fa-save"></i> Save
</button>
HTML;

echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-customer-info',
	'modal_title' => 'Cart :: Customer Information',
	'body' => $body,
	'foot' => $foot,
]);
