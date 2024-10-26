/**
 * [description]
 * @return {[type]} [description]
 */


$(function() {

	$('#pos-cart-option-save').on('click', function() {

		OpenTHC.POS.Cart.date = $('#cart-option-date').val();
		OpenTHC.POS.Cart.time = $('#cart-option-time').val();

		// var $b = $(this);
		// $b.html('<i class="fas fa-sync fa-spin"></i> Working...');
		// $b.attr('disabled', 'disabled');

		// var arg = {
		// 	a: 'checkout-option',
		// 	date: $('#checkout-option-date').val(),
		// 	time: $('#checkout-option-time').val(),
		// };

		$.post('/pos/cart/ajax', arg, function(body, stat) {
			$('#pos-modal-cart-option').modal('hide');
		});

	});
});
