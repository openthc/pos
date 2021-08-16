<?php
/**
 * Show List of Pending Inbound Transfer
 */
?>

<h1><a href="/b2b">Transfer</a> :: {{ Transfer.global_id }} <small>[{{ Transfer.manifest_type }} / {{ Transfer.status }}]</small></h1>

<form autocomplete="off" method="post">
<div class="container">

<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label>Manifest ID:</label>
			<input class="form-control" name="manifest-guid">
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label>From License:</label>
			<input class="form-control" name="source-license">
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label>Receiving License:</label>
			<input class="form-control" disabled name="target-license" readonly value="{{ License_Target.name }}">
		</div>
	</div>
</div>

<table class="table">
<thead>
	<tr>
	<th>Type</th>
	<th>GUID</th>
	<th>Strain</th>
	<th>Product</th>
	<th class="r">Quantity</th>
	<th class="r">Weight</th>
	<th class="r">Price</th>
	</tr>
</thead>
<tbody id="transfer-item-list">
<tr id="item-0">
	<td><?= _select_inventory_type('item-0') ?></td>
	<td><input class="form-control" name="inventory-guid-0"></td>
	<td><input class="form-control" name="strain-name-0"></td>
	<td><input class="form-control" name="product-name-0"></td>
	<td class="r"><input class="form-control math-input r" name="inventory-quantity-0"></td>
	<td class="r"><input class="form-control math-input r" name="inventory-weight-0"></td>
	<td class="r"><input class="form-control math-input r" name="inventory-price-0"></td>
</tr>
</tbody>
</table>



<div class="form-actions">
	<button class="btn btn-outline-primary" id="transfer-add-row" type="button" value="add-row"><i class="fas fa-plus"></i> Add Row</button>
	<button class="btn btn-outline-primary" name="a" type="submit" value="save"><i class="fas fa-save"></i> Save</button>
</div>

</div>
</form>


<script>
$(function() {

	$('#transfer-add-row').on('click', function() {

		var row0 = $('#item-0').clone(true);

		// Update the Names of this clone
		var nrid = Math.random().toString(36).substr(2, 9);

		row0.find('input,select').each(function(i, n) {
			var x = $(n).attr('name');
			x += '-';
			x += nrid;
			$(n).attr('name', x);
		});

		$('#transfer-item-list').append(row0);

	});

});
</script>
