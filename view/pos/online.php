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
		<a class="btn btn-outline-secondary">Configure Settings</a>
		<a class="btn btn-outline-secondary" href="/shop/example?c=<?= $_SESSION['Company']['id'] ?>" target="_blank">View Example</a>
	</div>
</div>

<div class="table-responsive">
<table class="table">
	<thead class="thead-dark">
		<tr>
			<th>Order</th>
			<th>Entered</th>
			<th>Total</th>
		</tr>
	</thead>
</table>
</div>

</div>
