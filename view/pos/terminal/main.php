<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

?>

<style>
#pos-camera-preview-wrap {
	background: #101010;
	border: 4px solid #c00000;
	border-radius: .25rem;
	left: 10vw;
	position: absolute;
	top: 5vh;
}
#pos-camera-preview-wrap .shut {
	position: absolute;
	top: -4px;
	right: -4px;
}
#pos-camera-preview-wrap video {
	transform: scaleX(-1);
}
</style>


<div id="pos-main-wrap">
	<div class="pos-item-sale-wrap">
	<div class="pos-item-wrap">
		<div style="display:flex; flex-direction: column; flex-wrap: nowrap; height:100%;">

			<!-- Scanner/Search Input Area -->
			<div id="pos-scanner-read" style="background: #ccc; flex: 1 0 auto; padding: 0.5rem;">
				<div class="input-group">
					<div class="input-group-prepend">
						<button class="btn btn-outline-secondary" id="pos-camera-input"><i class="fas fa-camera"></i></button>
					</div>
					<input class="form-control" id="barcode-input" name="barcode" type="text">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</div>

			<div id="pos-item-list">
				<!-- Filled by AJAX -->
			</div>
		</div>
	</div>
	<div class="pos-sale-wrap">
		<!-- Items on the Ticket -->
		<form action="/pos/pay" autocomplete="off" id="psi-form" method="post">
		<div id="psi-item-list" style="overflow-x:auto;">
			<div id="psi-item-list-empty" style="margin: 10%; text-align:center;">
				<h3 class="alert alert-dark">Purchase Ticket Data Appears Here</h3>
			</div>
		</div>
		</form>
	</div>
	</div> <!-- /.pos-item-sale-wrap -->
</div>

<div class="pos-foot-wrap">
	<div id="pos-terminal-sub-wrap">
		<div class="sub-info-item-wrap"><h3>Total: $<span class="pos-checkout-sub">0.00</span></h3></div>
		<div class="sub-info-item-wrap"><h3>Taxes: $<span class="pos-checkout-tax-sale">0.00</span></h3></div>
		<div class="sub-info-item-wrap"><h3>Due: $<span class="pos-checkout-sum">0.00</span></h3></div>
	</div>
	<div id="pos-terminal-cmd-wrap">
		<div class="cmd-item" style="flex: 0.5 0 auto;">
			<a class="btn btn-danger" href="/pos/shut"><i class="fas fa-power-off"></i></a>
			<a class="btn btn-warning" href="/pos" id="pos-shop-redo" type="button"><i class="fas fa-ban"></i></a>
		</div>
		<!--
		@deprecated this can be done by the Camera feature now
		<div class="cmd-item">
			<button class="btn btn-primary" id="pos-checkout-scan-id" data-toggle="modal" data-target="#pos-modal-scan-id" type="button">
				<i class="far fa-id-card"></i><span class="btn-text"> Scan ID</span>
			</button>
		</div>
		-->
		<div class="cmd-item">
			<button class="btn btn-secondary" data-toggle="modal" data-target="#pos-modal-sale-hold" disabled id="pos-shop-save" type="button">
				<i class="fas fa-save"></i><span class="btn-text"> Save</span></button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-secondary" data-toggle="modal" data-target="#pos-modal-discount" disabled id="pos-shop-disc" type="button">
				<i class="fas fa-percent"></i><span class="btn-text"> Discount</span>
			</button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-outline-success" data-toggle="modal" data-target="#pos-modal-loyalty" disabled type="button">
				<i class="fas fa-crown"></i><span class="btn-text"> Loyalty</span>
			</button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-success" data-toggle="modal" data-target="#pos-modal-payment" disabled id="pos-shop-next" type="button">
				<i class="far fa-money-bill-alt"></i><span class="btn-text"> Payment</span>
			</button>
		</div>
		<!-- <div class="" style="text-align:center;"><button class="good" disabled id="pos-pay-card" style="margin: 8px auto; width:80%;">Debit</button></div> -->
		<!-- <div class="" style="text-align:center;"><button class="good" disabled id="pos-pay-misc" style="margin: 8px auto; width:80%;">Other</button></div> -->
	</div>
</div>

<?php
echo $this->block('modal/pos/scan-id.php');
echo $this->block('modal/pos/hold.php');
echo $this->block('modal/pos/hold-list.php');
echo $this->block('modal/pos/discount.php');
echo $this->block('modal/pos/loyalty.php');
echo $this->block('modal/pos/payment-cash.php');
echo $this->block('modal/pos/payment-card.php');
// echo $this->block('modal/pos/card-swipe.php')
// echo $this->block('modal/pos/transaction-limit.php')
// echo $this->block('modal/pos/keypad.php')
?>

<script>
var body_drag = false;
var body_drag_y = 0;

function searchInventory(x)
{
	console.log('searchInventory(' + x + ')');
	$('#barcode-auto-complete').data('working', '');
	$('#pos-item-list').html('<h2 style="margin:16px;"><i class="fas fa-sync fa-spin"></i> Loading...</h2>');
	// var chk = $('#barcode-auto-complete').data('working');
	// $('#barcode-auto-complete').data('working', 'working');
	// $('#barcode-auto-complete').html('<h3>Searching...</h3>').show();
	// $('#pos-item-list').html('<h3>Searching...</h3>')
	//$('#barcode-auto-complete').load('/pos/ajax?a=search&b=' + x, function() {
	//	$('#barcode-auto-complete').data('working', '');
	//});
	$.get('/pos/ajax?a=search&q=' + x, function(body, stat) {
		$('#pos-item-list').html(body);
	}).fail(function(jxhr, stat) {
		if ('error' === stat) {
			if (404 === jxhr.status) {
				// Show some Message
				// $('#barcode-auto-complete').data('failure')
				$('#barcode-input').val('');
				$('#pos-item-list').html('<div class="alert alert-danger">No priced items were found</div>');
			}
		}
	});
}

$(function() {

	// https://github.com/zxing-js/library/blob/master/docs/examples/qr-camera/index.html
	$('#pos-camera-input').on('click', function() {
		window.OpenTHC.Camera.exists(function(good) {
			// window.OpenTHC.Camera.open(function(stream) {

			var html = [];
			html.push('<div id="pos-camera-preview-wrap">');
			html.push('<video id="pos-camera-preview" style="height:480px; width:640px;"></video>');
			html.push('<button class="btn btn-outline-danger shut"><i class="fas fa-times"></i></button>');
			html.push('</div>');
			$(document.body).append(html.join(''));

			// @see https://github.com/zxing-js/library/issues/432
			const hints = new Map();
			const formats = [
				ZXing.BarcodeFormat.CODE_128,
				ZXing.BarcodeFormat.QR_CODE,
				ZXing.BarcodeFormat.PDF_417
			];
			hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, formats);
			hints.set(ZXing.DecodeHintType.CHARACTER_SET, 'utf-8');
			//hints.set(ZXing.DecodeHintType.TRY_HARDER, true);
			//hints.set(ZXing.DecodeHintType.PURE_BARCODE, true);
			var Scanner = new ZXing.BrowserMultiFormatReader(hints);
			Scanner.decodeFromInputVideoDevice('', 'pos-camera-preview')
			.then(function(res) {
				console.log(res);
				$('#barcode-input').val(res);
				$('#pos-camera-preview-wrap').remove();
				Scanner.reset();
				delete Scanner;
			})
			.catch((err) => { console.log(err); });

			// window.OpenTHC.Camera.scan(function() {
			// 	alert('I Got a Scan!!');
			// });

			$('#pos-camera-preview-wrap .shut').one('click', function() {
				Scanner.reset();
				delete Scanner;
				$('#pos-camera-preview-wrap').remove();
			});

		});
	});

	$("#barcode-input").focus();
	$("#barcode-input").on('keyup', function() {

		var val = this.value;
		if (0 === val.length) {
			$('.pos-item-wrap .inv-item').show();
			return;
		}

		// Now Filter the Grid or Table
		var rex = new RegExp(val, 'gim');
		$('.pos-item-wrap .inv-item').each(function(i, n) {

			$(n).show();

			var t = $(n).text();
			if (!t.match(rex)) {
				$(n).hide();
			}

		});

	});

	searchInventory('');

	//$(document.body).on('touchstart', function(e) {
	//	body_drag = false;
	//	body_drag_y = e.originalEvent.touches[0].clientY;
	//});

	//$(document.body).on('touchmove', function(e) {
	//	var cur_y = e.originalEvent.touches[0].clientY;
	//	alert(body_drag_y);
	//	alert(cur_y);
	//	body_drag = true;
	//});

	$('#pos-item-list').on('click', '.inv-item', function(e) {

		if (body_drag) {
			return false;
		}

		Cart_addItem({
			id: $(this).data('id'),
			name: $(this).data('name'),
			weight: $(this).data('weight'),
			price: $(this).data('price'),
			qty: 1
		});

		return false;
	});

	$('#psi-item-list').on('click', '.fa-times', function() {

		if (body_drag) {
			return false;
		}

		var id = $(this).data('id');
		$('#psi-item-' + id).remove();

		chkSaleCost();
	});

	$('#pos-modal-sale-hold-save').on('click touchend', function(e) {
		$('#psi-form').attr('action', '/pos/cart/save');
		$('#psi-form').append('<input name="a" type="hidden" value="save">');
		$('#psi-form').append('<input name="name" type="hidden" value="' + $('#customer-name').val() + '">');
		$('#psi-form').submit();
		return false;
	});

	$('#pos-modal-scan-id').on('show.bs.modal', function() {
		POS.Scanner.live('#scan-input-stat', function(data) {

			var data = POS.Scanner.data.join('');
			data = data.replace(/Shift/g, ' ');
			data = data.replace(/ControlJ/g, '<br>--Control J--<br>');
			alert(data);
		});
	});

	$('#pos-modal-scan-id').on('hide.bs.modal', function() {
		POS.Scanner.stop();
	});

	$('#pos-modal-sale-hold-list').on('show.bs.modal', function() {
		$('#sale-hold-list-wrap').load('/pos/ajax?a=hold-list');
	});

	// $('#pos-pay-card').on('click', function(e) {
	// 	$('#modal-content-wrap').modal({
	//		 width:'80%',
	//		 title:'Modal Title',
	//		 dismiss:true,
	//		 showTitle:true
	//	 })
	// });
	// $('#pos-pay-misc').on('click', function(e) {
	// 	$('#modal-content-wrap').modal({
	//		 width:400,
	//		 title:'Modal Title',
	//		 dismiss:true,
	//		 showTitle:true
	//	 })
	// });

	//Weed.POS.push({});

	// Watch for Barcode Scanns Here
	// $(document).on('keypress', function() {
	// 	console.log('document!keypress');
	// });

//  If on-screen keyboard is muted, use this popup modal
//	$('#psi-item-list').on('focus', '.psi-item-size', function(e) {
//		Weed.modal( $('#pos-modal-number-input') );
//		$('#app-modal-wrap').css('width', '480px');
//		$('#pos-modal-number-input').show();
//		$('#pos-modal-number-input-live').html( parseFloat($(e.target).val(), 10).toFixed(2) );
//		$('#pos-modal-number-input-live').attr('data-first', 'true');
//		$('#pos-modal-number-input-live').attr('data-update', $(e.target).attr('id'));
//	});

	$('#pos-modal-number-input button.digit').on('click touchstart', function(e) {

		var cur = $('#pos-modal-number-input-live').html();
		if ('true' === $('#pos-modal-number-input-live').attr('data-first')) {
			cur = '';
		}
		var val = $(this).attr('data-key');

		switch (val) {
		case '0':
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
		case '6':
		case '7':
		case '8':
		case '9':
		case '.':
			cur += val.toString();
			break;
		case '<':
			cur = cur.slice(0, -1);
			break;
		}

		$('#pos-modal-number-input-live').html(cur);
		$('#pos-modal-number-input-live').attr('data-first', 'false')

		e.preventDefault();
		e.stopPropagation();
		return false;

	});

	$('#pos-modal-number-input #pmni-back').on('click', function(e) {
		Weed.modal('shut');
		return false;
	});

	$('#pos-modal-number-input #pmni-done').on('click', function(e) {

		e.preventDefault();
		e.stopPropagation();

		var nid = $('#pos-modal-number-input-live').attr('data-update');
		var node = $('#' + nid);
		node.val( $('#pos-modal-number-input-live').html() );
		node.change();
		chkSaleCost();

		Weed.modal('shut');

		return false;
	});
});
</script>

<?php
if ($data['cart_item_list']) {
?>
<script>
	<?php
	foreach ($data['cart_item_list'] as $ci) {
		printf('Cart_addItem(%s)', json_encode([
			'id' => $ci['id'],
			'name' => $ci['name'],
			'weight' => $ci['weight'],
			'price' => $ci['price'],
			'qty' => $ci['qty']
		]));
	}
	?>
</script>
<?php
}
