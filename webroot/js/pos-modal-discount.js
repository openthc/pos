/**
 * For the POS Modal for Discounts
 */

$(function() {

	var adj = 0;
	var adj_note = 'Applied Discount';
	var base_price = 0;
	var full_price = 0;
	var item_taxes_total = 0;

	function _update_adj_new(adj)
	{
		$('.pos-checkout-sum-adj').html( (adj).toFixed(2) );
		$('.pos-checkout-sum-new').html( (base_price + adj).toFixed(2) );
	}

	// Do stuff on Modal Open
	$('#pos-modal-discount').on('show.bs.modal', function() {

		console.log('pos-modal-discount!show');

		base_price = parseFloat($('.pos-checkout-sum').first().html()) || 0;

		//	$('#pos-modal-discount-list').load('/pos/ajax', { a: 'discount-list' });

	});

	// Detect Change and Reset Other One (fix resets pct)
	$('#pos-checkout-discount-fix').on('blur keyup', function(e) {

		var val = this.value;
		var fix = Math.abs(parseFloat(val) || 0);
		$('#pos-checkout-discount-pct').val('');

		adj = fix * -1;
		adj_note = `Applied Discount $${adj.toFixed(2)}`;
		_update_adj_new(adj);

	});

	// Percent Discount
	$('#pos-checkout-discount-pct').on('blur keyup', function(e) {

		var val = this.value;
		var pct = Math.abs(parseFloat(val) || 0);
		$('#pos-checkout-discount-fix').val('');

		if (pct <= 1) {
			pct = pct * 100;
		}
		adj = (base_price * pct / 100) * -1;
		adj_note = `Applied Discount ${pct.toFixed(0)}%`;

		_update_adj_new(adj);

	});

	// Add Line Item
	$('#pos-discount-apply').on('click', function() {
		Cart_addItem({
			id: window.ulid(),
			qty: 1,
			name: adj_note,
			price: adj,      // v1
			unit_price: adj  // v2
		});
	});

});
