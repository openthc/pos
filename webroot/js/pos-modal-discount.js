/**
 * For the POS Modal for Discounts
 */

$(function() {

	var adj = 0;
	var adj_note = 'Applied Discount';
	var due = 0;

	function _update_adj_new(adj)
	{
		if (adj < 0) {
			$('.pos-checkout-sum-adj').html( (adj).toFixed(2) );
			$('.pos-checkout-sum-new').html( (due + adj).toFixed(2) );
		}
	}

	// Do stuff on Modal Open
	$('#pos-modal-discount').on('show.bs.modal', function() {

		console.log('pos-modal-discount!show');

		due = parseFloat($('.pos-checkout-sum').first().html(), 10) || 0;

		//	$('#pos-modal-discount-list').load('/pos/ajax', { a: 'discount-list' });

	});


	// Detect Change and Reset Other One (fix resets pct)
	$('#pos-checkout-discount-fix').on('blur keyup', function(e) {

		var fix = Math.abs(parseFloat($(this).val(), 10) || 0);
		if (fix !== 0) {

			$('#pos-checkout-discount-pct').val('');

			adj = fix * -1;
			adj_note = `Applied Discount $${adj.toFixed(2)}`;
			_update_adj_new(adj);
		}
	});

	// Percent Discount
	$('#pos-checkout-discount-pct').on('blur keyup', function(e) {

		var pct = Math.abs(parseFloat($(this).val(), 10) || 0);
		if (pct !== 0) {

			$('#pos-checkout-discount-fix').val('');

			if (pct <= 1) {
				pct = pct * 100;
			}
			adj = (due * pct / 100) * -1;
			adj_note = `Applied Discount ${pct.toFixed(0)}%`;

			_update_adj_new(adj);
		}

	});

	$('#pos-discount-apply').on('click', function() {
		// Add Line I
		Cart_addItem({
			id: window.ulid(),
			qty: 1
			, name: adj_note
			, price: adj
			, unit_price: adj
		});

		$('#pos-modal-discount').modal
	});

});
