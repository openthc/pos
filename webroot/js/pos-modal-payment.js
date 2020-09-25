/**
 *
 */

var ppCashPaid_List = new Array();


function ppFormUpdate()
{
	var full = Weed.POS.sale.due;
	// var cash = parseFloat($('#pp-cash-pay').val(), 10);
	var cash = parseFloat($('#pp-cash-pay').text(), 10);
	var need = (full - cash);
	var back = (cash - full);

	console.log('ppFormUpdate(' + cash + ', ' + full + ')');


	if (cash < full) {

		$('#amount-paid-wrap').removeClass('alert-success');
		$('#amount-paid-wrap').addClass('alert-secondary');

		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success');
		$('#amount-need-wrap').addClass('alert-warning');
		$('#amount-need-hint').text('Due:');
		$('#amount-need-value').text(need.toFixed(2));

	} else if (cash === full) {

		// $('#pos-back-due').html('0.00');
		$('#amount-paid-wrap').removeClass('alert-secondary alert-success');
		$('#amount-paid-wrap').addClass('alert-success');

		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success alert-warning');
		$('#amount-need-wrap').addClass('alert-success');
		$('#amount-need-hint').text('Perfect!');
		$('#amount-need-value').text('0.00');

		// $('.pos-cost-due').addClass('text-success').removeClass('text-warning');
		// $('#pp-cash-pay').addClass('text-success').removeClass('text-danger').removeClass('text-warning');

		// $('#pos-payment-over').show().addClass('alert alert-success');
		$('#pos-payment-commit').removeAttr('disabled');

	} else if (cash > full) {

		$('#amount-paid-wrap').removeClass('alert-secondary alert-success');
		$('#amount-paid-wrap').addClass('alert-success');

		// Making Change
		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success');
		$('#amount-need-wrap').addClass('alert-danger');
		$('#amount-need-hint').text('Change:');
		$('#amount-need-value').text( back.toFixed(2) );
		// $('#pos-back-due').html( back.toFixed(2) )

		// $('.pos-cost-due').addClass('text-warning').removeClass('text-success');
		// $('#pp-cash-pay').addClass('text-warning').removeClass('text-danger').removeClass('text-success');
		// $('#pp-card-pay').removeClass('text-danger').removeClass('text-success').removeClass('text-warning');

		//$('#pos-back-due').addClass('text-warning').removeClass('text-danger').removeClass('text-success');
		// $('#pos-payment-over').show().addClass('alert alert-danger');
		$('#pos-payment-commit').removeAttr('disabled');

	}

	if (ppCashPaid_List.length == 0) {
		$('#pos-pay-undo').prop('disabled', true);
	} else {
		$('#pos-pay-undo').prop('disabled', false);
	}

}


function ppAddCash(n)
{
	console.log('ppAddCash');

	var full = $('#pos-cost-due').data('amount');

	var add = parseFloat( $(n).data('amount'), 10 );
	var cur = parseFloat( $('#pp-cash-pay').text(), 10 );
	if (!cur) cur = 0;

	var cash = (cur + add);
	$('#pp-cash-pay').text( cash.toFixed(2)  );

	var card = full - cash;
	$('#pp-card-pay').val( card.toFixed(2)  );

	ppCashPaid_List.push(add);

	ppFormUpdate();

}

function ppAddCard()
{
	console.log('ppAddCard');

	var need = $('#payment-need').val();
	$('#pp-card-confirm').val(need);

	//Weed.modal('shut');

	// var arg = $('#psi-form').serializeArray();
	// $('#modal-content-wrap').load('/pos/pay', arg, function() {
	// $.post('/pos/pay', arg, function(res) {

	// var x = $('#card-modal').clone();
	// $(x).find('#pp-card-confirm').attr('id', 'pp-card-prompt');
	//Weed.modal( $('#card-modal') );
	//$('#card-modal').show();


	// Weed.modal('#card-modal');
	// $('#pp-card-confirm').val( $('#pp-card-pay').val() );
	// Weed.modal('#card-modal');
	// });
}

$(function() {

	$('.pp-cash').on('click touchend', function(e) {
		ppAddCash(this);
		e.preventDefault();
		e.stopPropagation();
		return false;
	});

	$('.pp-card').on('click touchend', function(e) {
		ppAddCard();
		e.preventDefault();
		e.stopPropagation();
		return false;
	});

	// Focus on Select!
	// $('#pp-cash-pay').on('focus', function(e) {
	// 	console.log('focus');
	// 	$(this).select();
	// });
	// $('#pp-cash-pay').on('mouseup', function(e) {
	// 	console.log('mouseup');
	// 	e.preventDefault();
	// 	return false;
	// });

	$('#pp-cash-pay').on('keyup', function() {
		ppFormUpdate();
	});

	// Reset my Form
	$('#pos-pay-undo').on('click', function() {
		$('#pp-cash-pay').text('0.00');
		$('#pp-card-pay').text('0.00');
		ppFormUpdate();
	});

	$(document.body).on('click', '#pos-card-back', function() {
		//Weed.modal('shut');
	});

	$(document.body).on('click', '#pos-card-done', function() {
		//Weed.modal('shut');
	});

	// $('#pp-cash-pay').focus().select();
	// var ppcp = document.getElementById('pp-cash-pay');
	// ppcp.focus();
	// ppcp.select();

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
