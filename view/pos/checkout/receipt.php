<?php
/**
 * Receipt Options
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

?>

<div class="container mt-4">

<div class="row justify-content-center mb-2">
<div class="col-md-8">

	<div class="alert alert-success" style="font-size:28px;">
		<div class="d-flex justify-content-between">
			<div>Paid:</div>
			<div class="r">$<?= number_format($data['cash_incoming'], 2) ?></div>
		</div>
	</div>

	<div class="alert alert-danger" style="font-size:28px;">
		<div class="d-flex justify-content-between">
			<div>Change:</div>
			<div class="r">$<?= number_format($data['cash_outgoing'], 2) ?></div>
		</div>
	</div>

</div>
</div>

<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
	<h3 class="card-header">Print Receipt</h3>
	<div class="card-body">

		<form autocomplete="off" method="post">
		<div class="mb-2">
			<h4>Email</h4>
			<input name="sale_id" type="hidden" value="<?= $data['Sale']['id'] ?>">
			<div class="input-group">
				<input class="form-control" name="receipt-email" placeholder="client@email.com" type="email">
				<button class="btn btn-secondary" name="a" value="send-email"><i class="fas fa-envelope-open-text"></i> Email Receipt</button>
			</div>
		</div>
		</form>

		<form autocomplete="off" method="post">
		<div class="mb-2">
			<h4>Text/SMS</h4>
			<input name="sale_id" type="hidden" value="<?= $data['Sale']['id'] ?>">
			<div class="input-group">
				<input class="form-control" name="receipt-phone" placeholder="(###) ###-####" type="text">
				<button class="btn btn-secondary" name="a" value="send-phone"><i class="fas fa-sms"></i> Send Receipt</button>
			</div>
		</div>
		</form>

		<form autocomplete="off" method="post">
		<div class="mb-2">
			<h4>Print It</h4>
			<div class="input-group">
				<select class="form-control" id="printer-list">
					<option>- Select Printer -</option>
					<?php
					foreach ($data['printer_list'] as $p) {
						printf('<option data-local-link="%s" value="%s">%s</option>', $p['link'], $p['type'], __h($p['name']));
					}
					?>
				</select>

				<!-- <button class="btn btn-warning"
					formtarget="openthc-print-window" id="send-print" name="a" type="button"
					value="send-print"><i class="fas fa-print"></i> Print Receipt</button> -->

				<button class="btn btn-warning"
					id="send-print-frame"
					type="button"><i class="fas fa-print"></i> Print Receipt</button>

			</div>
			<p>Warning: Printing kills trees</p>
		</div>
		</form>

		<iframe id="print-frame" name="print-frame"
			src="/pos/checkout/receipt?s=<?= rawurldecode($_GET['id']) ?>&amp;a=pdf"
			style="border: 1px solid #000; width:100%;"></iframe>

	</div>


	<form autocomplete="off" method="post">
	<div class="card-footer">
		<button class="btn btn-primary" name="a" value="send-blank"><i class="fas fa-ban"></i> No Receipt</button>
	</div>
	</form>

</div>
</div>
</div>
</div>


<script>
function btnErrorFlash($btn)
{
	$btn.addClass('btn-outline-danger');
	setTimeout(function() {
		$btn.removeClass('btn-outline-danger');
	}, 1500);

}

$(function() {

	$('#send-print').on('click', function() {

		var $btn = $(this);
		var $sel = $('#printer-list').find(':selected');
		var val = $('#printer-list').val();

		// @todo if the selected printer is marked as "local-http"
		// Then we have to capture the PDF from the server
		// And then POST that PDF document to the specific server
		// Hopefully it works, it must be running our custom "print" server
		//var lpu = $('#print-list').val();

		// What ?  Popup?  Prompt for AIR-Print or Whatever?

		switch (val) {
		case 'air':

			btnErrorFlash($btn);

			return false;

			break;

		case 'app-print-direct':

			// Have App Generate One-Time Link
			// Then Pass to the app-container (Android, iOS, Electron) via CustomEvent
			$.post('', { a: 'print-direct-link' }, function(body, stat) {

					var ce = new CustomEvent('openthc_print_direct');
					ce.printer_url = $sel.data('local-link');
					ce.document_url = body.data.document_url;

					window.dispatchEvent(ce);
			});

			return false;

			break;

		case 'lpd':

			// Emit an Application Specific Event for Android, Electron or iOS to Catch?
			btnErrorFlash($btn);

			return false;

			break;

		case 'pdf': // @deprecated

			// Browser Popup
			var opts = [];
			opts.push('top=' + (window.screenTop + 64));
			opts.push('left=' + (window.screenLeft + 64));
			opts.push('width=' + (window.outerWidth - 128));
			opts.push('height=' + (window.outerHeight - 256));
			opts.push('location=yes');
			opts.push('scrollbars=yes');

			var w = window.open('/loading.html', 'openthc-print-window', opts.join(','));
			w.addEventListener('load', function() {
				console.log('onLoad!');
				setTimeout(function() {
					w.print();
				}, 1000);
			}, true);
			w.addEventListener('afterprint', function() {
				console.log('onAfterPrint!');
				// w.close();
			});

			break;

		case 'rpi':

			var lpu = $sel.data('local-link');
			if (lpu) {
				var url = window.location;
				POS.Printer.printLocalNetwork(url, lpu);
			} else {
				btnErrorFlash($btn);
			}

			return false;

			break;

		}

	});

	/**
	 * Print Frame w/PDF
	 */
	$('#send-print-frame').on('click', function() {

		var F = document.querySelector('#print-frame');
		// F.addEventListener('load', function() {

			// console.log('onLoad!');

			// Can't get these to fire
			// F.contentWindow.addEventListener('beforeprint', function() {
			// 	console.log('beforeprint!');
			// });

			// F.contentWindow.addEventListener('afterprint', function() {
			// 	console.log('onAfterPrint!');
			// });
			// F.contentWindow.onafterprint = function() {
			// 	console.log('onAfterPrint!');
			// };

			// setTimeout(function() {

				F.contentWindow.focus();
				F.contentWindow.print();

			// }, 250);

		// });

		return false;

	});

});
</script>
