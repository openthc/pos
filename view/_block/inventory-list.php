<table class="table table-sm">
<thead class="thead-dark">
	<tr>
		<th>Lot</th>
		<th>Product</th>
		<th>Package</th>
		<th class="r">QTY</th>
		<th class="r">$/ea</th>
	</tr>
</thead>
<tbody>
{% for inv in inventory_list %}

	<tr class="inv-item"
		data-id="{{ inv.id }}">

		<td><a href="/inventory/view?id={{ inv.id }}">{{ inv.guid }}</a></td>
		<td>{{ inv.product_name }}</td>
		<td>{{ inv.package_unit_qom }} {{ inv.package_unit_uom }}</td>
		<td class="r">{{ inv.qty }}</td>
		<td class="r">{{ inv.unit_price }}</td>

	</tr>
{% endfor %}
</tbody>
</table>