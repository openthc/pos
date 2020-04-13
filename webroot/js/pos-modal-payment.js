
$(function() {

	/**
		Actual Payment Button
	*/
	$('#pos-payment-commit').on('click touchend', function(e) {

		$('#psi-form').attr('action', '/pos/checkout/commit');
		$('#psi-form').append('<input name="a" type="hidden" value="pos-done">');
		$('#psi-form').append('<input name="due" type="hidden" value="' + Weed.POS.sale.due + '">');
		$('#psi-form').append('<input name="sub" type="hidden" value="' + Weed.POS.sale.sub + '">');
		$('#psi-form').append('<input name="tax_i502" type="hidden" value="' + Weed.POS.sale.tax_i502 + '">');
		$('#psi-form').append('<input name="tax_sale" type="hidden" value="' + Weed.POS.sale.tax_sale + '">');
		$('#psi-form').append('<input name="pay" type="hidden" value="' + $('#pp-cash-pay').val() + '">');
		$('#psi-form').append('<input name="name" type="hidden" value="' + $('#customer-name').val() + '">');
		$('#psi-form').submit();

		return false;

	});
});

// Alternate Payment Method Modal
/*
		// $('#psi-form').submit();

		// var arg = $('#psi-form').serializeArray();
		// // $('#modal-content-wrap').load('/pos/pay', arg, function() {
		// $.post('/pos/pay', arg, function(res) {
		// 	Weed.modal(res);
		// });

		var html = '';
		html+= '<div id="pos-payment-modal">';
		// html+= '<img src="http://chart.apis.google.com/chart?cht=qr&chs=400x400&chl=<?= rawurlencode('https://weedtraqr.com/pos/front?t=' . $_SESSION['pos-terminal-id']) ?>&chld=H|0">';
		html+= '</div>';

		Weed.modal(html);

		// var arr = $('#psi-form').serializeArray();
		// var obj = arr.reduce(function(o, v, i) {
		// 	o[i] = v;
		// 	return o;
		// }, {});

		$('#pos-payment-modal').load('/pos/pay', $('#psi-form').serializeArray(), function() {


		});
*/
