<?php
/**
 * Main Terminal View v2018
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

$head = sprintf('<h1>%s</h1>', _('Client Contact Verify:'));

ob_start();
?>

<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Customer Type:</div>
	<select	class="form-select form-select-lg"
		id="client-contact-type"
		name="client-contact-type"
		>
		<option value="018NY6XC00C0NTACTTYPE000AC">Adult / Recreational</option>
		<option value="018NY6XC00C0NTACTTYPE000PA">Medical Patient</option>
		<option value="018NY6XC00C0NTACTTYPE000CG">Medical Caregiver</option>
	</select>
</div>


<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Primary ID:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-guid"
		name="client-contact-guid"
		placeholder="Primary Identification Method"
		type="text"
		value="<?= __h($_SESSION['Cart']['Contact']['guid']) ?>">
</div>


<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Secondary ID:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-code"
		name="client-contact-code"
		placeholder="Secondary Identification Method"
		type="text"
		value="<?= __h($_SESSION['Cart']['Contact']['code']) ?>">
</div>


<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Full Name:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-name"
		name="client-contact-name"
		placeholder="First & Last"
		type="text"
		value="<?= __h($_SESSION['Cart']['Contact']['fullname']) ?>">
</div>


<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">DOB:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-dob"
		name="client-contact-dob"
		placeholder="MM/DD/YYYY or YYYY-MM-DD"
		type="text"
		value="<?= __h($_SESSION['Cart']['Contact']['meta']['dob']) ?>">
</div>


<!-- <div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Patient ID:</div>
	<input autocomplete="off"
		class="form-control form-control-lg contact-autocomplete"
		id="client-contact-pid"
		name="client-contact-pid"
		placeholder="AA-BBBB-CCCC-DDDD-EEEE-FFFF-GG"
		type="text"
		value="<?= __h($_SESSION['Cart']['Contact']['guid']) ?>">
</div>
 -->

<?php

switch ($_SESSION['cre']['id']) {
	case 'usa-ok':
		_contact_verify_info_usa_ok($_SESSION['Cart']['Contact']);
}

// var_dump($_SESSION['Cart']['Contact']);

$body = ob_get_clean();


$foot = [];
$foot[] = '<div class="d-flex justify-content-between">';
$foot[] = '<div>';
$foot[] = '<button class="btn btn-lg btn-primary" name="a" type="submit" value="client-contact-commit">Commit <i class="fas fa-arrow-right"></i></button>';
// $foot[] = '<button class="btn btn-lg btn-secondary" name="a" type="submit" value="client-contact-skip">Skip </button>';
// $foot[] = '<button class="btn btn-lg btn-secondary" name="a" type="submit" value="client-contact-search">Search </button>';
$foot[] = '</div>';
// $foot[] = '<div>';
// $foot[] ='<button class="btn btn-lg btn-warning" id="btn-form-reset" type="reset" value="client-contact-reopen">Reset </button>';
// $foot[] = '</div>';
$foot[] = '</div>';
$foot = implode(' ', $foot);

?>

<form action="/pos/checkout/open" autocomplete="off" method="post">
<div class="container mt-4">
	<?= _draw_html_card($head, $body, $foot) ?>
</div>
</form>

<?php

function _input_group($pre_text, $name_id, $hint, $val)
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
}

/**
 *
 */
function _contact_verify_info_usa_ok($Contact)
{
	$html = _input_group('License Type:', '', '', $Contact['meta']['@cre']['type']);
	echo str_replace('type="text"', 'readonly type="text"', $html);

	$html = _input_group('License Status:', '', '', $Contact['meta']['@cre']['status']);
	echo str_replace('type="text"', 'readonly type="text"', $html);

	$html = _input_group('License Expires:', '', '', $Contact['meta']['@cre']['expirationDate']);
	echo str_replace('type="text"', 'readonly type="text"', $html);

	// var_dump($Contact['meta']);
}
