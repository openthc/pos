/**
 * For the POS Modal for Discounts
 */

/*
// $('#pos-modal-discount').on('bd.moldal.show');
//$('#pos-shop-disc').on('click touchend', function(e) {
//
//	var chk = $(e.target).attr('disabled');
//	if (chk) {
//		e.preventDefault();
//		e.stopPropagation();
//		return false;
//	}
//
//	Weed.modal( $('#pos-modal-discount') );
//	$('#pos-modal-discount').show();
//	$('#pos-modal-discount-list').load('/pos/ajax', { a: 'discount-list' });
//	e.preventDefault();
//	e.stopPropagation();
//	return false;
//});
*/

$(function() {

	// Detect Change and Reset Other One (fix resets pct)
	$('#pos-checkout-discount-fix').on('blur keyup', function(e) {

		// Restrict to Number?
		var due = parseFloat($('.pos-checkout-sum').first().html(), 10) || 0;
		var fix = parseFloat($(this).val(), 10) || 0;

		var adj = fix;

		if (adj > 0) {
			$('#pos-checkout-discount-pct').val('');
			$('.pos-checkout-sum-adj').html( (adj * -1).toFixed(2) );
			$('.pos-checkout-sum-new').html( (due - adj).toFixed(2) );
		}

	});

	// Percent Discount
	$('#pos-checkout-discount-pct').on('blur keyup', function(e) {

		// Restrict to Number?
		var due = parseFloat($('.pos-checkout-sum').first().html(), 10) || 0;

		var pct = parseFloat($(this).val(), 10) || 0;
		if (pct >= 1) {
			pct = pct / 100;
		}
		var adj = (due * pct);

		if (adj > 0) {
			$('#pos-checkout-discount-fix').val('');
			$('.pos-checkout-sum-adj').html( (adj * -1).toFixed(2) );
			$('.pos-checkout-sum-new').html( (due - adj).toFixed(2) );
		}

	});

	$('#pos-discout-apply').on('click', function() {
		// Add Line Item
		//POS_Ticket.addLineItem({
		//	'guid' => '0000000000000000',
		//	'name' => 'Discount',
		//	'cost' => 1.23,
		//});
	});

});
