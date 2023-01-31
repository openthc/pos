<?php
/**
 * Main Terminal View v2018
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

$head = sprintf('<h1>%s</h1>', _('Client Information'));

$body = <<<HTML
<div class="row mb-4">
	<div class="col-md-6">
		<h5>DOB</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="client-contact-dob" name="client-contact-dob" type="text">
			<button class="btn btn-secondary" id="client-contact-dob" type="button"><i class="fas fa-camera"></i></button>
		</div>
	</div>
</div>


<div class="row mb-4">
	<div class="col-md-6">
		<h5>Patient ID</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="client-contact-pid" name="client-contact-pid" type="text">
			<!-- <div class="input-group-text"><i class="fas fa-hashtag"></i></div> -->
		</div>
	</div>
</div>
HTML;


$foot = '<button class="btn btn-lg btn-primary" name="a" type="submit" value="client-contact-update">Next </button>';

?>

<form autocomplete="off" method="post">
<div class="container mt-4">
<?= _draw_html_card($head, $body, $foot) ?>
</div>
</form>
