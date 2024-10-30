<?php
/**
 * Main Terminal View v2018
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$this->layout_file = sprintf('%s/view/_layout/html-pos.php', APP_ROOT);

?>

<div id="pos-main-wrap">
	<div class="pos-item-sale-wrap">
	<div class="pos-item-wrap">
		<div style="display:flex; flex-direction: column; flex-wrap: nowrap; height:100%;">

			<!-- Scanner/Search Input Area -->
			<div id="pos-scanner-read" style="background: #ccc; flex: 1 0 auto; padding: 0.5rem; position:relative;">
				<div class="input-group">
					<button class="btn btn-primary pos-camera-input" type="button"><i class="fas fa-camera"></i></button>
					<input autofocus class="form-control" id="barcode-input" name="barcode" type="text">
					<button class="btn btn-secondary" id="pos-inventory-search" type="button"><i class="fas fa-search"></i></button>
				</div>
				<div class="collapse" id="pos-scanner-read-info" style="background: #333; color: var(--bs-danger); font-size: 2rem; height: 100%; left: 0; position: absolute; text-align: center; top: 0; width:100%; z-index: 4;"></div>
			</div>

			<div id="pos-item-list">
				<!-- Filled by AJAX -->
				<div id="pos-item-list-empty" style="margin: 10%; text-align:center;">
					<h4 class="alert alert-dark">Inventory Search Data Appears Here</h4>
				</div>
			</div>
		</div>
	</div>
	<div class="pos-sale-wrap">
		<!-- Items on the Ticket -->
		<form action="" autocomplete="off" id="psi-form" method="post">
		<div id="cart-list-wrap" style="overflow-x:auto;">
			<div id="cart-list-empty" style="margin: 10%; text-align:center;">
				<h4 class="alert alert-dark">Purchase Ticket Data Appears Here</h4>
				<!-- <pre><?= __h(json_encode($data['cart'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?></pre> -->
			</div>
		</div>
		</form>
	</div>
	</div> <!-- /.pos-item-sale-wrap -->
</div>

<div class="pos-foot-wrap">
	<div id="pos-terminal-sub-wrap">
		<div class="sub-info-item-wrap"><h3>Items: #<span class="pos-checkout-item-count">0</span></h3></div>
		<div class="sub-info-item-wrap"><h3>Total: $<span class="pos-checkout-sub">0.00</span></h3></div>
		<div class="sub-info-item-wrap"><h3>Taxes: $<span class="pos-checkout-tax-total">0.00</span></h3></div>
		<div class="sub-info-item-wrap"><h3>Final: $<span class="pos-checkout-sum">0.00</span></h3></div>
	</div>
	<div id="pos-terminal-cmd-wrap">
		<!-- @deprecated this can be done by the Checkin or Camera feature now	-->
		<!-- <div class="cmd-item">
			<button class="btn btn-lg btn-primary" id="pos-checkout-scan-id" data-bs-toggle="modal" data-bs-target="#pos-modal-scan-id" type="button">
				<i class="far fa-id-card"></i><span class="btn-text"> Scan ID</span>
			</button>
		</div> -->
		<!--
		<div class="cmd-item">
			<button class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#pos-modal-customer-info" type="button">
				<i class="fas fa-user"></i><span class="btn-text"> Customer</span>
			</button>
		</div>
		 -->
		<div class="cmd-item">
			<button class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#pos-modal-sale-hold" disabled id="pos-shop-save" type="button">
				<i class="fas fa-save"></i><span class="btn-text"> Save</span></button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-lg btn-secondary" data-bs-toggle="modal" data-bs-target="#pos-modal-discount" disabled type="button">
				<i class="fas fa-percent"></i><span class="btn-text"> Discount</span>
			</button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#pos-modal-loyalty" disabled type="button">
				<i class="fas fa-crown"></i><span class="btn-text"> Loyalty</span>
			</button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-lg btn-secondary"
				data-bs-toggle="modal"
				data-bs-target="#pos-modal-cart-option"
				disabled
				id="pos-ticket-options"
				type="button">
				<i class="fa-solid fa-wrench"></i><span class="btn-text"> Options</span></button>
		</div>
		<div class="cmd-item">
			<button class="btn btn-lg btn-success" data-bs-toggle="modal" data-bs-target="#pos-modal-payment" disabled id="pos-shop-next" type="button">
				<i class="far fa-money-bill-alt"></i><span class="btn-text"> Payment</span>
			</button>
		</div>
		<!-- <div class="" style="text-align:center;"><button class="good" disabled id="pos-pay-card" style="margin: 8px auto; width:80%;">Debit</button></div> -->
		<!-- <div class="" style="text-align:center;"><button class="good" disabled id="pos-pay-misc" style="margin: 8px auto; width:80%;">Other</button></div> -->
	</div>
</div>

<?php
// echo $this->block('modal/pos/scan-id.php');
// echo $this->block('modal/pos/customer-info.php');
echo $this->block('modal/pos/hold.php');
echo $this->block('modal/pos/discount.php');
echo $this->block('modal/pos/loyalty.php');
echo $this->block('modal/pos/cart-options.php');
echo $this->block('modal/pos/payment-cash.php');
echo $this->block('modal/pos/payment-card.php');
// echo $this->block('modal/pos/card-swipe.php')
// echo $this->block('modal/pos/transaction-limit.php')
// echo $this->block('modal/pos/keypad.php')
?>

<template id="b2c-item-row-template">
<div class="container pb-2 inv-item cart-item" style="opacity:0.50"
	data-id="{$obj->id}"
	data-inventory-id="{$obj->id}"
	data-weight="{$obj->weight}"
	id="psi-item-{$obj->id}">

	<div class="row">
		<div class="col-md-10" style="vertical-align:baseline;"><h4>{$obj->name}</h4></div>
		<div class="col-md-2" style="text-align:right;">
			<button class="btn b2c-item-remove" type="button">
				<i class="fas fa-times text-danger"></i>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="input-group">
				<label class="input-group-text">Qty:</label>
				<input class="form-control b2c-item-unit-count"
					data-id="{$obj->id}"
					id="psi-item-{$obj->id}-unit-count"
					name="item-{$obj->id}-unit-count" type="number" value="{$obj->unit_count}">
			</div>
		</div>
		<div class="col-md-4">
			<div class="input-group">
				<input class="form-control b2c-item-unit-price"
					data-id="{$obj->id}"
					id="psi-item-{$obj->id}-unit-price"
					name="item-{$obj->id}-unit-price"
					type="number" value="{$obj->unit_price}">
				<label class="input-group-text">ea</label>
			</div>
		</div>
		<div class="col-md-4" style="text-align:right;">
			<h3><span class="b2c-item-unit-price-total" id="psi-item-{$obj->id}-full-price">{$obj->unit_price_total}</span></h3>
		</div>
	</div>
</div>
</template>


<script>
OpenTHC.POS.Cart.id = '<?= $data['cart']['id'] ?>';
</script>

<script>
/**
 *
 */
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
	fetch(`/pos/ajax?a=search&q=${x}`)
		.then(res => res.text())
		.then(body => {
			$('#barcode-input').val('');
			$('#pos-item-list').html(body);
		});

}

$(function() {

	$("#barcode-input").on('keyup', function() {

		var val = this.value;
		if (0 === val.length) {
			$('#pos-item-list .inv-item').show();
			return;
		}

		// Now Filter the Grid or Table
		var rex = new RegExp(val, 'gim');
		$('#pos-item-list .inv-item').each(function(i, n) {

			$(n).show();

			var t = $(n).text();
			if (!t.match(rex)) {
				$(n).hide();
			}

		});

	});

	$('#pos-item-list').on('click', '.inv-item', function(e) {

		OpenTHC.POS.Cart.insert({
			id: $(this).data('id'),
			name: $(this).data('name'),
			weight: $(this).data('weight'),
			unit_price: $(this).data('price'),
			unit_count: 1,
			unit_price_total: '?.??',
		});

		return false;
	});

	$('#pos-modal-sale-hold-save').on('click touchend', function(e) {
		// @todo use fetch() and FormData()
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

	// Watch for Barcode Scanns Here
	// $(document).on('keypress', function() {
	// 	console.log('document!keypress');
	// });

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
		$('pos-modal-number-input').modal('hide');
		return false;
	});

	$('#pos-modal-number-input #pmni-done').on('click', function(e) {

		e.preventDefault();
		e.stopPropagation();

		var nid = $('#pos-modal-number-input-live').attr('data-update');
		var node = $('#' + nid);
		node.val( $('#pos-modal-number-input-live').html() );
		node.change();
		OpenTHC.POS.Cart.update();

		$('pos-modal-number-input').modal('hide');

		return false;
	});

	<?php
	if ( ! empty($data['cart']['item_count'])) {
		echo "$('#cart-list-empty').html('<div class=\"alert alert-warning\">Loading...</div>');\n";
		echo "\tOpenTHC.POS.Cart.reload();\n";
	}
	?>

});
</script>
