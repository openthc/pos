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

			<input id="client-contact-id" name="client-contact-id" type="hidden" value="">

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

<!--
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
		</div>
	</div>
</div>
 -->

<!--
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
 -->

<!--
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
				class="form-control form-control-lg contact-autocomplete"
				id="client-contact-pid"
				name="client-contact-pid"
				placeholder="AA-BBBB-CCCC-DDDD-EEEE-FFFF-GG"
				type="text">
		</div>
	</div>
</div>
-->
HTML;

$foot = [];
$foot[] = '<div class="d-flex justify-content-between">';
$foot[] = '<div>';
$foot[] = '<button class="btn btn-lg btn-primary" name="a" type="submit" value="client-contact-update">Next </button>';
// $foot[] = '<button class="btn btn-lg btn-secondary" name="a" type="submit" value="client-contact-search">Search </button>';
$foot[] = '</div>';
$foot[] = '<div>';
$foot[] ='<button class="btn btn-lg btn-warning" id="btn-form-reset" type="reset" value="client-contact-reopen">Reset </button>';
$foot[] = '</div>';
$foot[] = '</div>';
$foot = implode(' ', $foot);

?>

<form action="/pos/checkout/open" autocomplete="off" method="post">
<div class="container mt-4">
<?= _draw_html_card($head, $body, $foot) ?>
</div>
</form>

<script>
var $govInput;
var scan_buffer = [];
var text_buffer = [];

function _checkout_open_reopen()
{
	scan_buffer = [];
	text_buffer = [];
	if ($govInput) {
		$govInput.attr('readonly', false);
		$govInput.val('');
		$govInput.focus();
	}
}

$(function() {

	$govInput = $('#client-contact-govt-id');

	$govInput.on('focus', function() {
		$('#client-contact-govt-id-scanner').addClass('btn-success').removeClass('btn-outline-secondary');
	});

	$govInput.on('blur', function() {
		$('#client-contact-govt-id-scanner').addClass('btn-outline-secondary').removeClass('btn-success');
	});

	// Scanner Keydown Handler
	var scan_time = 0;
	var scan_skip_next = false;
	$govInput.on('keydown', function(e) {

		console.log(`${e.key} (${e.code}); skip:${scan_skip_next}`);
		// if ( ! scan_time) {
		// 	// scan_time = now()
		// }

		if (scan_skip_next) {
			e.preventDefault();
			scan_skip_next = false;
			return false;
		}

		switch (e.key) {
			case 'Backspace':
			case 'Delete':
				text_buffer.pop();
				return true;
			case 'Control':
				e.preventDefault();
				scan_skip_next = true;
				break;
			case 'Enter':
			case 'Meta':
			// case 'Shift':
				e.preventDefault();
				break;
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

		if (val.match(/^[\w\s\/\-\+]$/)) {
			text_buffer.push(val);
			$govInput.val( text_buffer.join('') );
		}

		return false;

	});

	$govInput.on('keyup', _.debounce(function() {

		var val = scan_buffer.join('');
		console.log(`scanned-val:${val}`);

		if (val.length >= 20) {
			var rex = new RegExp('ANSI.+DCS.+DAC', 'ms');
			if (rex.test(val)) {

				// It's a PDF417 Scan
				var $self = $(this);
				$self.attr('readonly', true);

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

				// Some have this odd thing, so we replace it out
				val = val.replace(/DB\[Shift\]B/, 'DBB');
				var dob = val.match(/DBB(\d{2})(\d{2})(\d{4})/);
				console.log(dob);

				$govInput.val(`${govST[1]} / ${govID[1]}`);
				$('#client-contact-name').val(`${nameF[1]} ${nameM[1]} ${nameL[1]}`);
				$('#client-contact-dob').val(`${dob[3]}-${dob[1]}-${dob[2]}`);
				$('#client-contact-pid').focus();

				fetch(`/contact/ajax?term=${govID[1]}`)
					.then(res => res.json())
					.then(function(res) {
						debugger;
					});
			}
		}
	}, 250));

	$('#client-contact-govt-id-scanner').on('click', _checkout_open_reopen);

	$('.contact-autocomplete').autocomplete({
		source: '/contact/ajax',
		select: function(e, ui) {
			// debugger;
			$('#client-contact-id').val(ui.item.id);
		}
	});

	$('#btn-form-reset').on('click', _checkout_open_reopen);

});
</script>
