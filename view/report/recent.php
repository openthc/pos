<?php
/**
 * Report View
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

?>

<div>
<table class="table table-sm">
<thead class="thead-dark">
	<tr>
	<th>Sale</th>
	<th>Date</th>
	<th>Contact</th>
	<th>Total</th>
	<th>Status</th>
	<th></th>
	</tr>
</thead>
<tbody>

<?php
foreach ($data['b2c_transactions'] as $b2c_transaction) {
	$m = json_decode($b2c_transaction['meta'], true);
	$full_price = $b2c_transaction['full_price'] ?: $m['due'];
	$full_price = sprintf('$%0.2f', $full_price);
	switch ($b2c_transaction['stat']) {
		case 100:
			$status = 'Open';
			$action_ux = '<button class="btn btn-outline-success" data-b2c-id="%s" id="exec-push-transaction" type="button">';
			$action_ux.= '<i class="fas fa-upload"></i>';
			$action_ux.= '</button>';
			$action_ux = sprintf($action_ux, $b2c_transaction['id']);
			break;
		default:
			$status = 'Closed';
			$action_ux = '';
	}
?>
	<tr>
	<td><a href=""><?= $b2c_transaction['guid'] ?></a></td>
	<td><?= $b2c_transaction['created_at'] ?></td>
	<td><?= $b2c_transaction['contact_id'] ?></td>
	<td><?= $full_price ?></td>
	<td><?= $status ?></td>
	<td><?= $action_ux ?></td>
	</tr>
<?php
}
?>

</tbody>
</table>
</div>

<script>
$(function() {
	$('#exec-push-transaction').on('click', function(e) {
		var transaction_id = $(this).data('b2c-id')
		$.ajax({
			type: 'POST',
			url: '/report/ajax',
			data: {
				a: 'push-transaction',
				id: transaction_id,
			},
			success: function(body, ret) {
				window.location.reload();
			},
		});
	});
});
</script>

