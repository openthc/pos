/**
 * Stuff for the POS.Cart()
 */


/**
	Add Item to POS Active Ticket
*/
function Cart_addItem(obj)
{
	//var inv = $(n).clone();
	//var inv_id = $(inv).data('id');
	if ( ! obj.weight) {
		obj.weight = '';
	}

	if ( ! obj.qty ) {
		obj.qty = 1;
	}
	obj.qty = parseFloat(obj.qty, 10) || 0;
	obj.price = parseFloat(obj.price, 10) || 0;

	// @todo Move Clicked Item to Top Row
	if ($('#psi-item-' + obj.id).length > 0) {
		var q = $('#psi-item-' + obj.id + '-size').val();
		q = parseFloat(q, 10) || 1;
		obj.qty += q;
		$('#psi-item-' + obj.id).remove();
	}

	var full_price = obj.price * obj.qty;

	// @note why have inv-item on here?
	var html = `
<div class="inv-item psi-item-item"
	data-id="${obj.id}"
	data-weight="${obj.weight}"
	data-price="${obj.price}"
	id="psi-item-${obj.id}">

<div class="row" style="margin:0;">
	<div class="col-md-10" style="vertical-align:baseline;"><h4>${obj.name}</h4></div>
	<div class="col-md-2" style="text-align:right;"><i class="fas fa-times" data-id="${obj.id}" style="color:#f00; cursor:grab; font-size: 24px; margin:8px;"></i></div>
</div>
<div class="row" style="margin:0;">
	<div class="col-md-4">
		<input class="form-control psi-item-size" data-id="${obj.id}" id="psi-item-${obj.id}-size" name="qty-${obj.id}" type="number" value="${obj.qty}">
	</div>
	<div class="col-md-4">
		<h3 style="margin:0; padding:0;">${obj.price.toFixed(2)}</h3>
	</div>
	<div class="col-md-4" style="text-align:right;">
		<h3><span id="psi-item-${obj.id}-sale">${full_price.toFixed(2)}</span></h3>
	</div>
</div>
</div>`;

	$('#cart-list-wrap').prepend( html );

	Cart_addItem_flash(obj.id);
	chkSaleCost();

	Weed.POS.Ticket.checkSaleLimits();

	$('#cart-list-empty').hide();
}


function Cart_addItem_Alt(obj)
{

}


/**
	Blink the Item
*/

function Cart_addItem_flash(inv_id)
{
	// $('#psi-item-' + inv_id + '-size').focus();
	$('#psi-item-' + inv_id + '-size').addClass('pos-warn');
	$('#psi-item-' + inv_id + '-sale').addClass('pos-warn');
	setTimeout(function() {
		$('#psi-item-' + inv_id + '-size').removeClass('pos-warn');
		$('#psi-item-' + inv_id + '-sale').removeClass('pos-warn');
	}, 321);
}

$(function() {

	// The Remove Button
	$('#cart-list-wrap').on('click', '.fa-times', function() {

		// Remove Parent
		$(this).closest('.psi-item-item').remove();

		chkSaleCost();

	});

	// If on-screen keyboard is muted, use this popup modal
	//	$('#cart-list-wrap').on('focus', '.psi-item-size', function(e) {
	//		Weed.modal( $('#pos-modal-number-input') );
	//		$('#app-modal-wrap').css('width', '480px');
	//		$('#pos-modal-number-input').show();
	//		$('#pos-modal-number-input-live').html( parseFloat($(e.target).val(), 10).toFixed(2) );
	//		$('#pos-modal-number-input-live').attr('data-first', 'true');
	//		$('#pos-modal-number-input-live').attr('data-update', $(e.target).attr('id'));
	//	});

});
