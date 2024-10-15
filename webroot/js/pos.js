/* global $:false, OpenTHC:false */
/* eslint browser:0, multivar:1, white:1, no-console: "off" */
/**
	Point-of-Sale JavaSCript
*/

// @see https://github.com/ulid/javascript/blob/master/dist/index.js
// so this is not realy a ULID thing
window.ulid = function()
{
	var t = Date.now();
	var r0 = Math.floor(Math.random() * 100000);
	var r1 = Math.floor(Math.random() * 1000000);
	var u = t.toString(32).toUpperCase() + r0.toString(32).toUpperCase() + r1.toString(32).toUpperCase();
	return u;
}


var OpenTHC = OpenTHC || {};

OpenTHC.POS = {

	Cart: {
		unit_count: 0,
		unit_price: 0,
		tax_total: 0,
		full_price: 0,
	},

	init_done:false,
	init:function(m)
	{
		// Some times the browswer can fire load twice?, so trap that
		if (OpenTHC.POS.init_done) {
			console.log('OpenTHC.POS.init() - 2nd');
			return;
		}

		switch (m) {
		case 'front':
			// Save O as terminal ID?
			OpenTHC.POS.pull();
			setInterval(function() {
				OpenTHC.POS.pull();
			}, 2345);
			break;
		}

		OpenTHC.POS.init_done = true;

	},
	ping_tick: null,
	ping: function() {
		$.get('/pos/ajax?a=ping');
		if (null === OpenTHC.POS.ping_tick) {
			OpenTHC.POS.ping_tick = setInterval(OpenTHC.POS.ping, 60 * 1000);
			return;
		}
	},
	pull: function() {
		$.get('/pos/ajax?a=pull', function(res, ret, xhr) {
			switch (xhr.status) {
			case 200:
				$('#pos-front-view').html(res);
				break;
			case 304:
				// Ignore
				break;
			}
		});
	},
	push: function(fd)
	{
		$.post('/pos/ajax?a=push', fd);
	},
	sale: {
		due: 0,
		sum: 0,
		tax_sale: 0,
		setVal: function(k, v) {
			var self = this; // The Sale. object
			self[k] = v;
			// Recalc All the Things?
		}
	}
};


OpenTHC.POS.Ticket = {

	checkSaleLimits: function()
	{
		var cur_22 = 0; // Solid Infused
		var cur_23 = 0; // Liquid Infused
		var cur_24 = 0; // Extract to Inhale
		var cur_28 = 0; // Usable

		$('.cart-item').each(function(i, n) {

			var inv_id = $(n).data('id');

			var q = $(`#psi-item-${inv_id}-unit-count`).val();
			if (isNaN(q)) {
				console.log('OpenTHC.POS.Ticket.checkSaleLimits - Bad Quantity for Item: ' + inv_id);
				return(0);
			}

			var w = $(n).data('weight');
			if (isNaN(w)) {
				console.log('OpenTHC.POS.Ticket.checkSaleLimits - Bad Weight for Item: ' + inv_id);
				return(0);
			}

			var k = $(n).data('kind');
			switch (k) {
			case 22:
				cur_22 += (w * q);
				break;
			case 23:
				cur_23 += (w * q);
				break;
			case 24:
				cur_24 += (w * q);
				break;
			case 28:
				cur_28 += (w * q);
				break;
			}

		});

		var pass = true;
		if (cur_22 > 453) {
			pass = false;
		}
		if (cur_23 > 2000) {
			pass = false;
		}
		if (cur_24 > 7) {
			pass = false;
		}
		if (cur_28 > 28) {
			pass = false;
		}

		if (!pass) {
			Weed.modal( $('#pos-modal-transaction-limit') );
			$('#pos-modal-transaction-limit').show();
		}

	}

};



function chkSaleCost()
{
	console.log('chkSaleCost()');

	OpenTHC.POS.sale.sub = 0;

	// Find All Line Items
	$('.cart-item').each(function(x, n) {

		var inv_id = $(n).data('id');
		var unit_count = $(`#psi-item-${inv_id}-unit-count`).val();
		if (isNaN(unit_count)) {
			console.log('chkSaleCost ! invalid unit_count');
		}

		var unit_price = $(`#psi-item-${inv_id}-unit-price`).val();
		unit_price = parseFloat(unit_price);

		OpenTHC.POS.Cart.full_price += (unit_count * unit_price);
		OpenTHC.POS.sale.sub += (unit_count * unit_price);

	});

	if (isNaN(OpenTHC.POS.sale.sub)) {
		OpenTHC.POS.sale.sub = 0;
	}

	OpenTHC.POS.sale.due	  = OpenTHC.POS.sale.sub + OpenTHC.POS.sale.tax_sale;

	// Canonical
	$('.pos-checkout-sub').html(parseFloat(OpenTHC.POS.sale.sub, 10).toFixed(2));
	$('.pos-checkout-tax-total').html(parseFloat(OpenTHC.POS.sale.tax_sale, 10).toFixed(2));
	$('.pos-checkout-sum').html(parseFloat(OpenTHC.POS.sale.due, 10).toFixed(2));

	if (OpenTHC.POS.sale.due <= 0) {
		$('.pos-checkout-sum').parent().css('color', '');
		$('#pos-terminal-cmd-wrap button').attr('disabled', 'disabled');
	} else if (OpenTHC.POS.sale.due > 0) {
		$('.pos-checkout-sum').parent().css('color', '#f00000');
		$('#pos-terminal-cmd-wrap button').removeAttr('disabled');
	}

	OpenTHC.POS.push($('#psi-form').serializeArray());

}

$(function() {

	// Click the Cancel Button
	$('.pos-checkout-reopen').on('click touchend', function(e) {
		$(document.body).empty();
		$(document.body).css({
			'background': '#101010',
			'color': '#eeeeee',
		});
		$(document.body).html('<h1 style="margin:5em; text-align:center;"><i class="fas fa-sync fa-spin"></i> Loading...</h1>');
	});

	// Shop SHUT?

	// Attach Handler to Payment Button
	$('#pos-shop-next').on('click', function(e) {

		$('#payment-cash-incoming').val('0.00');

		ppFormUpdate();

	});

	// https://github.com/zxing-js/library/blob/master/docs/examples/qr-camera/index.html
	$('.pos-camera-input').on('click', function() {

		debugger;

		$btn = $(this);

		window.OpenTHC.Camera.exists(function(good) {

			if ( ! good) {
				$btn.removeClass('btn-primary');
				$btn.addClass('btn-danger');
				// $btn.prop('disabled', true);
				$('#pos-scanner-read-info').text('Invalid Camera Device');
				$('#pos-scanner-read-info').show();
				setTimeout(function() {
					$('#pos-scanner-read-info').hide();
				}, 5000);

				return;
			}

			// window.OpenTHC.Camera.open(function(stream) {

			var html = [];
			html.push('<div id="pos-camera-preview-wrap">');
			html.push('<video id="pos-camera-preview" style="height:480px; width:640px;"></video>');
			html.push('<div class="close-wrap">');
			html.push('<button class="btn btn-outline-danger shut"><i class="fas fa-times"></i></button>');
			html.push('</div>');
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
			// var Scanner = new ZXing.BrowserMultiFormatReader(hints);
			var Scanner = new ZXing.BrowserPDF417Reader(); // hints);
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


	/**
		An Item Size has Changed
	*/
	$(document).on('change', '.cart-item input', function(e) {

		console.log('.cart-item input!change');

		var inv_id = this.getAttribute('data-id');
		var unit_count = parseFloat( $(`#psi-item-${inv_id}-unit-count`).val() );
		var unit_price = parseFloat( $(`#psi-item-${inv_id}-unit-price`).val() );
		var item_price = unit_count * unit_price;

		$(`#psi-item-${inv_id}-full-price`).html(item_price.toFixed(2));

		if (unit_count <= 0) {
			$('#psi-item-' + inv_id).remove();
		}

		chkSaleCost();
	});

	// Open A Link in a Modal Thing
	$('.qrcode-link').on('click', function(e) {

		function draw_qrcode(code, size)
		{
			var arg = {
				text: code,
				width: size,
				height: size,
				colorDark : "#000000",
				colorLight : "#ffffff",
				correctLevel : QRCode.CorrectLevel.H
			};
			$('#qrcode-embed-image').empty();
			var qrcode = new QRCode('qrcode-embed-image', arg);

		}

		var size = 384;

		var html = `<div class="modal" id="qrcode-view" role="dialog" tabindex="-1">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-body">
	<div style="height: ${size}px; margin: 0 auto; width: ${size}px;">
		<div id="qrcode-embed-image"><i class="fas fa-sync fa-spin"></i></div>
	</div>
</div>
</div>
</div>
</div>
`;

		$(window.document.body).append(html);
		$('#qrcode-view').modal('show');
		$('#qrcode-view').on('hide.bs.modal', function() {
			$('#qrcode-view').remove();
		});

		var code = this.getAttribute('data-code');
		if (code) {
			draw_qrcode(code, size);
			return;
		}

		var load = this.getAttribute('data-load');
		if (load) {
			fetch(load).then(res => res.json()).then(function(json) {
				draw_qrcode(json.data, size);
			});
		}

	});

	$('#pos-lot-search').on('click', function() {
		var val = document.querySelector('#barcode-input').value;
		searchInventory(val);
	});

	// Load Hold List
	window.document.addEventListener('menu-left-opened', function() {
		const $shl = $('#sale-hold-list');
		$shl.html('<h4><i class="fas fa-sync fa-spin"></i> Loading...</h4>');
		fetch('/pos/ajax?a=hold-list')
		.then(res => res.text())
		.then(html => {
			$shl.html(html);
		});
	});

	// Load the Sale Ticket
	$('#sale-hold-list').on('click', 'a', function() {
		console.log('sale-hold-list a!click');
		$('#menu-left').removeClass('open');
		fetch(`/pos/ajax?a=hold-open&id=${this.hash.replace('#', '')}`)
		.then(res => res.json())
		.then(json => {
			json.data.forEach(function(v, i) {
				Cart_addItem({
					id: v.id,
					name: v.product.name,
					weight: v.package.name,
					price: v.unit_price,
					qty: v.qty
				});
			});
		});

	});

	$('#sale-hold-list').on('click', 'button', function() {
		// console.log('sale-hold-list button!click');
		var $x = $(this).closest('.sale-hold-list-item');
		fetch(`/pos/ajax?id=${this.value}`, {
			method:'DELETE'
		});
		$x.remove();

	});

});
