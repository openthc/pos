/**
 * [description]
 * @return {[type]} [description]
 */


$(function() {

	$('#pos-loyalty-apply').on('click', function() {

		var $b = $(this);
		$b.html('<i class="fas fa-sync fa-spin"></i> Working...');
		$b.attr('disabled', 'disabled');

		var arg = {
			a: 'loyalty',
			phone: $('#loyalty-phone').val(),
			email: $('#loyalty-email').val(),
			other: $('#loyalty-other').val(),
		};

		$.post('/pos/cart/ajax', arg, function(body, stat) {
			Cart_addItem({
				id: 0,
				name: body.data.name,
				weight: '-',
				price: body.data.rank,
				size: 1,
			});
			$('#pos-modal-loyalty').modal('hide');
		})

	});
});
