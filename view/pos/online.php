<?php
/**
 * External / Online Orders
 */

?>

<div class="container">
<div class="hero">
	<h1>Online Orders</h1>
	<p>These orders can come from external systems, if you have them configured or from your own website</p>
	<div>
		<a class="btn btn-outline-secondary" href="/settings/external">Configure Settings</a>
		<a class="btn btn-outline-secondary" href="/shop/example?c=<?= $_SESSION['Company']['id'] ?>" target="_blank">View Example</a>
	</div>
</div>

<div class="table-responsive">
<table class="table">
	<thead class="thead-dark">
		<tr>
			<th>Order</th>
			<th>Contact</th>
			<th>Details</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($data['b2c_sale_hold'] as $rec) {

		$b2b_sale = json_decode($rec['meta'], true);
		$rec['contact_name'] = $b2b_sale['contact']['name'];
		$item_info = [];
		foreach ($b2b_sale['item_list'] as $i => $v) {
			$item_info[] = sprintf('%s %s', $v['product']['name'], $v['variety']['name']);
		}
		$rec['item_info'] = implode(', ', $item_info);

		echo '<tr>';
		printf('<td><a href="/pos/online%s</td>', $rec['id']);
		printf('<td>%s</td>', $rec['contact_name']);
		// printf('<td><pre>%s</pre></td>', $rec['meta']);
		printf('<td>%s</td>', $rec['item_info']);
		echo '</tr>';

	}
	?>
	</tbody>
</table>
</div>

</div>
