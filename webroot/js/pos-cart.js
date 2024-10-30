/**
 * Stuff for the POS.Cart()
 */

'use strict';

OpenTHC.POS.Cart = {

	date: '',
	time: '',

	item_list: {},

	item_count: 0,  // Count of Line Items
	unit_count: 0,  // Total Count of Units
	unit_price_total: 0,  // Total of Line Items (line_price?)
	tax_total: 0,
	full_price: 0,

	drawItem: function(obj)
	{
		$('#cart-list-empty').hide();

		// Prepare UI
		var sel = `#psi-item-${obj.id}`;
		var b2b_item_row_output = document.querySelector(sel);
		if (b2b_item_row_output) {
			this.drawItemUpdate(b2b_item_row_output, obj);
		} else {
			// Add Placeholder
			var b2b_item_row_src = document.querySelector('#b2c-item-row-template');
			b2b_item_row_output = b2b_item_row_src.content.cloneNode(true);

			this.drawItemUpdate(b2b_item_row_output.querySelector('div'), obj);

			$('#cart-list-wrap').prepend(b2b_item_row_output);
		}
	},
	drawItemUpdate: function(row, obj)
	{
		row.setAttribute('data-id', obj.id);
		row.setAttribute('data-inventory-id', obj.id);
		row.setAttribute('data-weight', obj.weight);
		row.setAttribute('id', `psi-item-${obj.id}`);
		row.querySelector('h4').innerHTML = obj.name;

		var tmp = row.querySelector('input.b2c-item-unit-count');
		tmp.setAttribute('id', `psi-item-${obj.id}-unit-count`);
		tmp.setAttribute('name', `item-${obj.id}-unit-count`);
		tmp.setAttribute('value', obj.unit_count);

		var tmp = row.querySelector('input.b2c-item-unit-price');
		tmp.setAttribute('id', `psi-item-${obj.id}-unit-price`);
		tmp.setAttribute('name', `item-${obj.id}-unit-price`);
		tmp.setAttribute('value', obj.unit_price);

		var tmp = row.querySelector('span.b2c-item-unit-price-total');
		tmp.setAttribute('id', `psi-item-${obj.id}-full-price`);
		tmp.innerHTML = '<X>' + obj.unit_price_total || '0.00';
		// tmp.setAttribute('name', `item-${obj.id}-unit-price`);
		// tmp.setAttribute('value', obj.unit_price);

	},

	/**
	 * Insert Item
	 */
	insert: function(obj) {

		var Cart0 = this;

		var x = document.querySelector(`#psi-item-${obj.id}`);
		if (x) {
			x.style.opacity = 0.50;
		}

		// Add Existing
		if (Cart0.item_list[obj.id]) {
			obj.unit_count = Cart0.item_list[obj.id].unit_count + 1;
		}

		Cart0.item_list[obj.id] = obj;

		Cart0.drawItem(obj);
		Cart0.update(obj.id);

	},
	/**
	 * Delete Item
	 */
	delete: function(inv_id) {

		var x = document.querySelector(`#psi-item-${inv_id}`);
		x.style.opacity = 0.50;

		var Cart0 = this;
		if (Cart0.item_list[inv_id]) {
			Cart0.item_list[inv_id].unit_count = 0;
		}

		Cart0.update(inv_id);

	},

	reload: function()
	{
		var Cart0 = this;

		var fd0 = new FormData();
		fd0.set('a', 'cart-reload');
		fd0.set('cart', JSON.stringify({ id: Cart0.id }));

		fetch('/pos/cart/ajax', {
			method: 'POST',
			body: fd0
		}).then(res => {
			return res.json();
		}).then(res => {
			// Update HTML Somehwere?
			Cart0.item_list = res.data.Cart.item_list;

			Object.keys(Cart0.item_list).forEach(key => {
				var obj = Cart0.item_list[key];
				Cart0.drawItem( obj );
				Cart_addItem_flash(obj.id);
			});

			Cart0.updateSummary(res.data);
		});

	},

	/**
	 * Send to Server and Update UI
	 */
	update: function(inv_id) {

		var Cart0 = this;

		// Update Server
		var fd0 = new FormData();
		fd0.set('a', 'cart-update');
		fd0.set('cart', JSON.stringify(Cart0));

		fetch('/pos/cart/ajax', {
			method: 'POST',
			body: fd0
		}).then(res => {
			return res.json();
		}).then(res => {

			console.log(res);

			if (inv_id) {
				var obj1 = res.data.Cart.item_list[ inv_id ];
				if (obj1) {
					Cart0.drawItem(obj1);
					Cart_addItem_flash(inv_id);
				} else {
					$(`#psi-item-${inv_id}`).remove();
				}
			}

			Cart0.updateSummary(res.data);

		});

	},

	/**
	 * Update the Summary Footer
	 */
	updateSummary: function(res_data)
	{
		var Cart0 = this;

		Cart0.item_count =       res_data.Cart.item_count || 0;
		Cart0.unit_count =       res_data.Cart.unit_count || 0;
		Cart0.tax_total  =       res_data.Cart.tax_total  || 0;
		Cart0.full_price =       res_data.Cart.full_price || 0;
		Cart0.unit_price_total = res_data.Cart.unit_price_total || 0;

		$('.pos-checkout-item-count').html( Cart0.unit_count );
		$('.pos-checkout-sub').html(Cart0.unit_price_total);
		$('.pos-checkout-tax-total').html(Cart0.tax_total);
		$('.pos-checkout-sum').html(Cart0.full_price);

		if (Cart0.full_price <= 0) {
			$('.pos-checkout-sum').parent().css('color', '');
			$('#pos-terminal-cmd-wrap button').attr('disabled', 'disabled');
		} else if (Cart0.full_price > 0) {
			$('.pos-checkout-sum').parent().css('color', '#f00000');
			$('#pos-terminal-cmd-wrap button').removeAttr('disabled');
		}

	}

};

/**
	Blink the Item
*/
function Cart_addItem_flash(inv_id)
{
	// $(`#psi-item-${inv_id}-unit-count`).focus();
	var x = document.querySelector(`#psi-item-${inv_id}`);
	x.style.opacity = 1;

	$(`#psi-item-${inv_id}-unit-count`).addClass('text-danger');
	$(`#psi-item-${inv_id}-full-price`).addClass('text-danger');

	setTimeout(function() {
		$(`#psi-item-${inv_id}-unit-count`).removeClass('text-danger');
		$(`#psi-item-${inv_id}-full-price`).removeClass('text-danger');
	}, 321);
}

$(function() {

	// Remove Item and Update
	$('#cart-list-wrap').on('click', '.b2c-item-remove', function() {
		var oid = $(this).closest('.cart-item').data('id');
		OpenTHC.POS.Cart.delete(oid);
		OpenTHC.POS.Cart.update();
	});

	// If on-screen keyboard is muted, use this popup modal
	//	$('#cart-list-wrap').on('focus', '.cart-item input', function(e) {
	//		Weed.modal( $('#pos-modal-number-input') );
	//		$('#app-modal-wrap').css('width', '480px');
	//		$('#pos-modal-number-input').show();
	//		$('#pos-modal-number-input-live').html( parseFloat($(e.target).val()).toFixed(2) );
	//		$('#pos-modal-number-input-live').attr('data-first', 'true');
	//		$('#pos-modal-number-input-live').attr('data-update', $(e.target).attr('id'));
	//	});

	/**
		An Item Size has Changed
	*/
	$('#cart-list-wrap').on('change', '.cart-item input', function(e) {

		console.log('.cart-item input!change');

		var $item = $(this).closest('.cart-item');
		$item.css('opacity', 0.50);

		var inv_id = $item.data('id');
		var unit_count = parseFloat( $(`#psi-item-${inv_id}-unit-count`).val() );
		var unit_price = parseFloat( $(`#psi-item-${inv_id}-unit-price`).val() );
		var item_price = unit_count * unit_price;

		$(`#psi-item-${inv_id}-full-price`).html(item_price.toFixed(2));

		var Cart0 = OpenTHC.POS.Cart;

		// Update Local Object
		if (Cart0.item_list[inv_id]) {
			Cart0.item_list[inv_id].unit_count = unit_count;
			Cart0.item_list[inv_id].unit_price = unit_price;
		}

		// Delete or Update
		if (unit_count <= 0) {
			Cart0.delete(inv_id);
		} else {
			Cart0.update(inv_id);
		}

	});

});
