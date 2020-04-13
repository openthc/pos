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
	if (!obj.weight) {
		obj.weight = '';
	}

	var size = 1;

	// @todo Move Clicked Item to Top Row
	if ($('#psi-item-' + obj.id).length > 0) {
		var q = $('#psi-item-' + obj.id + '-size').val();
		size = parseFloat(q, 10) || 1;
		size++;
		$('#psi-item-' + obj.id).remove();
	}

	var sale = obj.price * size;

	var html = '';
	html += '<div class="inv-item psi-item-item"';
	html += ' data-id="' + obj.id + '"';
	html += ' data-kind="' + obj.kind + '"';
	html += ' data-weight="' + obj.weight + '"';
	html += ' data-price="' + obj.price + '"';
	html += ' id="psi-item-' + obj.id + '">';

	// html += '<input name="item-' + inv_id + '" type="hidden" value="' + inv_id + '">';

	html += '<div class="row" style="margin:0;">';
		html += '<div class="col-md-10" style="vertical-align:baseline;"><h4>';
		html += obj.name;
		html += '</h4></div>';
		html += '<div class="col-md-2" style="text-align:right;"><i class="fas fa-times" data-id="' + obj.id + '" style="color:#f00; cursor:grab; font-size: 24px; margin:8px;"></i></div>';
	html += '</div>';

	html += '<div class="row" style="margin:0;">';
		html += '<div class="col-md-4" style="text-align:center;">';
		html += obj.weight;
		html += '</div>';
		html += '<div class="col-md-4" style="text-align:center;">';
		html += '<input class="form-control psi-item-size" data-id="' + obj.id + '" id="psi-item-' + obj.id + '-size" name="qty-' + obj.id + '" type="number" value="' + size + '">';
		html += '</div>';
		html += '<div class="col-md-4" style="text-align:right;"><h3 style="margin:0; padding:0;"><span id="psi-item-' + obj.id + '-sale">' + sale.toFixed(2) + '</span></h3></div>';
	html += '</div>';

	html += '</div>';

	$('#psi-item-list').prepend( html );

	Cart_addItem_flash(obj.id);
	chkSaleCost();

	Weed.POS.Ticket.checkSaleLimits();

	$('#psi-item-list-empty').hide();
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
