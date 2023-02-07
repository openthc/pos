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
	<div class="input-group-text">Full Name:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-name"
		name="client-contact-name"
		placeholder="First & Last"
		type="text"
		value="<?= __h($_SESSION['Checkout']['Contact']['fullname']) ?>">
</div>


<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">DOB:</div>
	<input autocomplete="off"
		class="form-control form-control-lg"
		id="client-contact-dob"
		name="client-contact-dob"
		placeholder="MM/DD/YYYY or YYYY-MM-DD"
		type="text"
		value="<?= __h($_SESSION['Checkout']['Contact']['meta']['dob']) ?>">
</div>


<div class="alert alert-secondary">
	If necessary, such as medical sales, input their Patient ID <em><strong>exactly</strong></em> as it appears on their patient identification card.
</div>

<div class="input-group input-group-lg mb-4">
	<div class="input-group-text">Patient ID:</div>
	<input autocomplete="off"
		class="form-control form-control-lg contact-autocomplete"
		id="client-contact-pid"
		name="client-contact-pid"
		placeholder="AA-BBBB-CCCC-DDDD-EEEE-FFFF-GG"
		type="text"
		value="<?= __h($_SESSION['Checkout']['Contact']['guid']) ?>">
</div>

<?php
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
var_dump($_SESSION['Checkout']['Contact']);
?>
