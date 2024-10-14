/**
 *
 */

var ppCashPaid_List = new Array();


function ppFormUpdate()
{
	var full = Weed.POS.sale.due;
	var cash = parseFloat($('#payment-cash-incoming').text(), 10);
	var need = (full - cash);
	var back = (cash - full);

	console.log('ppFormUpdate(' + cash + ', ' + full + ')');


	if (cash < full) {

		$('#amount-paid-wrap').removeClass('alert-success');
		$('#amount-paid-wrap').addClass('alert-secondary');

		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success');
		$('#amount-need-wrap').addClass('alert-warning');
		$('#amount-need-hint').text('Due:');
		$('#payment-cash-outgoing').text(need.toFixed(2));

	} else if (cash === full) {

		// $('#pos-back-due').html('0.00');
		$('#amount-paid-wrap').removeClass('alert-secondary alert-success');
		$('#amount-paid-wrap').addClass('alert-success');

		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success alert-warning');
		$('#amount-need-wrap').addClass('alert-success');
		$('#amount-need-hint').text('Perfect!');
		$('#payment-cash-outgoing').text('0.00');

		// $('.pos-cost-due').addClass('text-success').removeClass('text-warning');
		// $('#payment-cash-incoming').addClass('text-success').removeClass('text-danger').removeClass('text-warning');

		$('#pos-payment-commit').removeAttr('disabled');

	} else if (cash > full) {

		$('#amount-paid-wrap').removeClass('alert-secondary alert-success');
		$('#amount-paid-wrap').addClass('alert-success');

		// Making Change
		$('#amount-need-wrap').removeClass('alert-danger alert-secondary alert-success');
		$('#amount-need-wrap').addClass('alert-danger');
		$('#amount-need-hint').text('Change:');
		$('#payment-cash-outgoing').text( back.toFixed(2) );
		// $('#pos-back-due').html( back.toFixed(2) )

		// $('.pos-cost-due').addClass('text-warning').removeClass('text-success');
		// $('#payment-cash-incoming').addClass('text-warning').removeClass('text-danger').removeClass('text-success');
		// $('#pp-card-pay').removeClass('text-danger').removeClass('text-success').removeClass('text-warning');

		//$('#pos-back-due').addClass('text-warning').removeClass('text-danger').removeClass('text-success');
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

	var full = $('#pos-cost-due').data('amount'); // @todo is this wrong?

	var add = parseFloat( $(n).data('amount'), 10 );
	var cur = parseFloat( $('#payment-cash-incoming').text(), 10 );
	if (!cur) cur = 0;

	var cash = (cur + add);
	$('#payment-cash-incoming').text( cash.toFixed(2)  );

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
	// $('#payment-cash-incoming').on('focus', function(e) {
	// 	console.log('focus');
	// 	$(this).select();
	// });
	// $('#payment-cash-incoming').on('mouseup', function(e) {
	// 	console.log('mouseup');
	// 	e.preventDefault();
	// 	return false;
	// });

	// $('#payment-cash-incoming').on('keyup', function() {
	// 	ppFormUpdate();
	// });

	// Reset my Form
	$('#pos-pay-undo').on('click', function() {
		$('#payment-cash-incoming').text('0.00');
		$('#pp-card-pay').text('0.00');
		ppFormUpdate();
	});

	$(document.body).on('click', '#pos-card-back', function() {
		//Weed.modal('shut');
	});

	$(document.body).on('click', '#pos-card-done', function() {
		//Weed.modal('shut');
	});

	/**
		Actual Payment Button
	*/
	$('#pos-payment-commit').on('click touchend', function(e) {

		var cash_incoming = $('#payment-cash-incoming').text();
		var cash_outgoing = $('#payment-cash-outgoing').text();

		$('#psi-form').attr('action', '/pos/checkout/commit');
		$('#psi-form').append('<input name="a" type="hidden" value="pos-done">');
		$('#psi-form').append('<input name="due" type="hidden" value="' + Weed.POS.sale.due + '">');
		$('#psi-form').append('<input name="sub" type="hidden" value="' + Weed.POS.sale.sub + '">');
		$('#psi-form').append('<input name="tax_sale" type="hidden" value="' + Weed.POS.sale.tax_sale + '">');
		$('#psi-form').append('<input name="pay" type="hidden" value="' + $('#payment-cash-incoming').val() + '">'); // @deprecated
		$('#psi-form').append('<input name="name" type="hidden" value="' + $('#customer-name').val() + '">');
		$('#psi-form').append(`<input name="cash_incoming" type="hidden" value="${cash_incoming}">`);
		$('#psi-form').append(`<input name="cash_outgoing" type="hidden" value="${cash_outgoing}">`);
		$('#psi-form').submit();

		return false;

	});

});
