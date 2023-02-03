<?php
/**
 * Main Terminal View v2018
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

$head = sprintf('<h1>%s</h1>', _('Check In:'));

$body = <<<HTML

<div class="alert alert-secondary">Scan the client's identification card or input their identification details.</div>


<div class="row mb-4">
	<div class="col-md-6">
		<h5>Identification:</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" autofocus
				class="form-control form-control-lg"
				id="client-contact-govt-id"
				name="client-contact-govt-id"
				placeholder="State / ID"
				type="text">

			<button class="btn btn-success"
				id="client-contact-govt-id-scanner"
				type="button"><i class="fas fa-qrcode"></i></button>

			<button class="btn btn-secondary pos-camera-input"
				data-camera-callback=""
				x-id="client-contact-dob-camera"
				type="button"><i class="fas fa-camera"></i></button>
		</div>
	</div>
</div>


<div class="row mb-4">
	<div class="col-md-6">
		<h5>Name:</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off"
				class="form-control form-control-lg"
				id="client-contact-name"
				name="client-contact-name"
				placeholder="First & Last"
				type="text">
			<!-- <button class="btn btn-secondary" id="pos-camera-input" x-id="client-contact-dob" type="button"><i class="fas fa-camera"></i></button> -->
		</div>
	</div>
</div>


<div class="row mb-4">
	<div class="col-md-6">
		<h5>DOB:</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off"
				class="form-control form-control-lg"
				id="client-contact-dob"
				name="client-contact-dob"
				placeholder="MM/DD/YYYY or YYYY-MM-DD"
				type="text">
		</div>
	</div>
</div>

<div class="alert alert-secondary">
	If necessary, such as medical sales, input their Patient ID <em><strong>exactly</strong></em> as it appears on their patient identification card.
</div>

<div class="row mb-4">
	<div class="col-md-6">
		<h5>Patient ID</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off"
				class="form-control form-control-lg"
				id="client-contact-pid"
				name="client-contact-pid"
				placeholder="AA-BBBB-CCCC-DDDD-EEEE-FFFF-GG"
				type="text">
			<!-- <div class="input-group-text"><i class="fas fa-hashtag"></i></div> -->
		</div>
	</div>
</div>

HTML;


$foot = '<button class="btn btn-lg btn-primary" name="a" type="submit" value="client-contact-update">Next </button>';
$foot.= ' <button class="btn btn-lg btn-secondary" name="a" type="submit" value="client-contact-search">Search </button>';
$foot.= ' <button class="btn btn-lg btn-secondary" name="a" type="submit" value="client-contact-reopen">Reset </button>';

?>

<form action="/pos/checkout/open" autocomplete="off" method="post">
<div class="container mt-4">
<?= _draw_html_card($head, $body, $foot) ?>
</div>
</form>

<script>
$(function() {

	$('#client-contact-govt-id').on('focus', function() {
		$('#client-contact-govt-id-scanner').addClass('btn-success').removeClass('btn-outline-secondary');
	});

	$('#client-contact-govt-id').on('blur', function() {
		$('#client-contact-govt-id-scanner').addClass('btn-outline-secondary').removeClass('btn-success');
	});

	// Scanner Keydown Handler
	var scan_buffer = [];
	var scan_skip_next = false;
	$('#client-contact-govt-id').on('keydown', function(e) {

		console.log(`${e.key} (${e.code}); skip:${scan_skip_next}`);

		if (scan_skip_next) {
			scan_skip_next = false;
			return false;
		}

		var val = e.key;

		switch (val) {
			case 'Control':
			case 'Enter':
			case 'Meta':
			case 'Shift':
				val = `[${val}]`;
				break;
		}

		// if (e.key.length == '1') {
		scan_buffer.push(val);
		// }

		switch (e.key) {
			case 'Control':
				scan_skip_next = true;
				break;
			case 'Enter':
			case 'Meta':
			case 'Shift':
				break;
		}

		if (val.match(/^[\w\s\/]$/)) {
			console.log('allow');
			// return true;
		}

		return false;

	});

	$('#client-contact-govt-id').on('keyup', _.debounce(function() {

		var val = scan_buffer.join('');
		console.log(`scanned-val:${val}`);

		if (val.length >= 20) {
			var rex = new RegExp('ANSI.+DCS.+DAC', 'ms');
			if (rex.test(val)) {

				// It's a PDF417 Scan
				var $self = $(this);
				$self.attr('disabled', true);

				var govST = val.match(/DAJ(\w+)/);
				console.log(govST);

				var govID = val.match(/DAQ(\w+)/);
				console.log(govID);

				var nameF = val.match(/DAC(\w+)/);
				console.log(nameF);

				var nameL = val.match(/DCS(\w+)/);
				console.log(nameL);

				var nameM = val.match(/DAD(\w+)/);
				console.log(nameM);

				var dob = val.match(/DB\[Shift\]B(\d{2})(\d{2})(\d{4})/);
				console.log(dob);

				$('#client-contact-govt-id').val(`${govST[1]} / ${govID[1]}`);
				$('#client-contact-name').val(`${nameF[1]} ${nameM[1]} ${nameL[1]}`);
				$('#client-contact-dob').val(`${dob[3]}-${dob[1]}-${dob[2]}`);
				$('#client-contact-pid').focus();

			}
		}
	}, 250));

	$('#client-contact-govt-id-scanner').on('click', function() {
		scan_buffer = [];
		$('#client-contact-govt-id').attr('disabled', false);
		$('#client-contact-govt-id').val('');
		$('#client-contact-govt-id').focus();
	});

});
</script>
