/**
 * Stuff for the POS.Cart()
 */

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
		var chk = `#psi-item-${obj.id}`;
		if ($(chk).length > 0) {
			$(chk).remove();
		}

		// Add Placeholder
		var b2b_item_row_src = document.querySelector('#b2c-item-row-template');
		var b2b_item_row_output = b2b_item_row_src.content.cloneNode(true);

		b2b_item_row_output.querySelector('div.cart-item').setAttribute('data-id', obj.id);
		b2b_item_row_output.querySelector('div.cart-item').setAttribute('data-inventory-id', obj.id);
		b2b_item_row_output.querySelector('div.cart-item').setAttribute('data-weight', obj.weight);
		b2b_item_row_output.querySelector('div.cart-item').setAttribute('id', `psi-item-${obj.id}`);
		b2b_item_row_output.querySelector('h4').innerHTML = obj.name;
		b2b_item_row_output.querySelector('i.b2c-item-remove').setAttribute('id', `btn-${obj.id}-delete`);
		b2b_item_row_output.querySelector('i.b2c-item-remove').setAttribute('data-id', obj.id);

		var tmp = b2b_item_row_output.querySelector('input.b2c-item-unit-count');
		tmp.setAttribute('id', `psi-item-${obj.id}-unit-count`);
		tmp.setAttribute('name', `item-${obj.id}-unit-count`);
		tmp.setAttribute('value', obj.qty);
		tmp.setAttribute('data-id', obj.id);

		var tmp = b2b_item_row_output.querySelector('input.b2c-item-unit-price');
		tmp.setAttribute('id', `psi-item-${obj.id}-unit-price`);
		tmp.setAttribute('name', `item-${obj.id}-unit-price`);
		tmp.setAttribute('value', obj.unit_price);
		tmp.setAttribute('data-id', obj.id);

		var tmp = b2b_item_row_output.querySelector('span.b2c-item-unit-price-total');
		tmp.setAttribute('id', `psi-item-${obj.id}-full-price`);
		tmp.innerHTML = '<X>' + obj.unit_price_total || '0.00';
		// tmp.setAttribute('name', `item-${obj.id}-unit-price`);
		// tmp.setAttribute('value', obj.unit_price);
		// tmp.setAttribute('data-id', obj.id);

		$('#cart-list-wrap').prepend(b2b_item_row_output);

	},

	/**
	 * Insert Item
	 */
	insert: function(obj) {

		$('#cart-list-empty').hide();

		var Cart0 = this;

		// Add Existing
		if (Cart0.item_list[obj.id]) {
			obj.qty = Cart0.item_list[obj.id].qty + 1;
		}
		Cart0.item_list[obj.id] = obj;

		Cart0.drawItem(obj);

		// Update Server
		var fd0 = new FormData();
		fd0.set('a', 'cart-insert');
		fd0.set('cart', JSON.stringify({ id: Cart0.id }));
		fd0.set('item', JSON.stringify(obj));

		fetch('/pos/cart/ajax', {
			method: 'POST',
			body: fd0
			// headers: {
			// 	'content-type': 'application/json'
			// }
		}).then(res => {
			return res.json();
		}).then(res => {

			// console.log(res.data.Cart);
			// console.log('Update Landed Item (or drawItem again?)');
			var obj1 = res.data.Cart.item_list[ obj.id ];
			Cart0.drawItem(obj1);

			// Draw Item Again?
			var x = document.querySelector(`#psi-item-${obj.id}`);
			x.style.opacity = 1;

			// Update HTML Somehwere?

			Cart0.updateSummary(res.data);
			// Draw from Server (call another repaint / template routine?)

			Cart_addItem_flash(obj.id);

		});

	},
	/**
	 * Delete Item
	 */
	delete: function(oid) {
		// this.update();
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

				// Mark Solid
				var obj = Cart0.item_list[key];
				Cart0.drawItem( obj );
				var x = document.querySelector(`#psi-item-${obj.id}`);
				x.style.opacity = 1;

				Cart_addItem_flash(obj.id);
			});

			Cart0.updateSummary(res.data);
		});

	},

	/**
	 * Send to Server and Update UI
	 */
	update: function() {

		var Cart0 = this;

		// Find All Line Items
		var item_list = [];
		$('.cart-item').each(function(x, n) {

			var b2c_item = {};

			b2c_item.inventory_id = $(n).data('id');
			b2c_item.unit_count = $(`#psi-item-${b2c_item.inventory_id}-unit-count`).val();
			b2c_item.unit_count = parseFloat(b2c_item.unit_count);
			b2c_item.unit_price = $(`#psi-item-${b2c_item.inventory_id}-unit-price`).val();
			b2c_item.unit_price = parseFloat(b2c_item.unit_price);

			if (isNaN(b2c_item.unit_count)) {
				// console.log('Cart.update() ! invalid unit_count');
				return;
			}
			if (b2c_item.unit_count <= 0) {
				return;
			}

			item_list.push(b2c_item);

		});

		// Update Server
		var fd0 = new FormData();
		fd0.set('a', 'cart-update');
		fd0.set('cart', JSON.stringify({ id: Cart0.id }));
		fd0.set('item_list', JSON.stringify(item_list));

		fetch('/pos/cart/ajax', {
			method: 'POST',
			body: fd0
		}).then(res => {
			return res.json();
		}).then(res => {

			console.log(res);

			// Update HTML Somehwere?
			Cart0.updateSummary(res.data);

			// Cart_addItem_flash(obj.id);

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
	$(`#psi-item-${inv_id}-unit-count`).addClass('text-danger');
	$(`#psi-item-${inv_id}-full-price`).addClass('text-danger');
	setTimeout(function() {
		$(`#psi-item-${inv_id}-unit-count`).removeClass('text-danger');
		$(`#psi-item-${inv_id}-full-price`).removeClass('text-danger');
	}, 321);
}

$(function() {

	// The Remove Button
	$('#cart-list-wrap').on('click', '.fa-times', function() {

		// Remove Parent
		$(this).closest('.cart-item').remove();

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

});
