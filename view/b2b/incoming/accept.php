<?php
/**
 * Show List of Pending Inbound Transfer
 */
?>

<h1>Transfer :: <?= $data['Transfer']['global_id'] ?>
<small>[<?= $data['Transfer']['manifest_type'] ?> / <?= $data['Transfer']['status'] ?>]</small></h1>

<div class="row">
<div class="col">
	<div class="form-group">
		<label>From:</label>
		<div class="input-group">
			<input class="form-control" readonly value="<?= $data['Origin_License']['name'] ?> #<?= $data['Origin_License']['code'] ?>">
			<div class="input-group-append"><a class="btn btn-outline-secondary" href="https://directory.openthc.com/company?id=<?= $data['Origin_License']['company_id'] ?>" target="_blank"><i class="fas fa-address-book"></i></a></div>
		</div>
		<small><?= $data['Transfer']['global_from_mme_id'] ?></small>
	</div>
</div>
<div class="col">
	<div class="form-group">
		<label>Ship To:</label>
		<div class="input-group">
			<input class="form-control" readonly value="<?= $data['Target_License']['name'] ?> #<?= $data['Target_License']['code'] ?>">
			<div class="input-group-append"><a class="btn btn-outline-secondary" href="https://directory.openthc.com/company?id=<?= $data['Demand_License']['company_id'] ?>" target="_blank"><i class="fas fa-address-book"></i></a></div>
		</div>
		<small><?= $data['Transfer']['global_to_mme_id'] ?></small>
	</div>
</div>
</div>

<hr>

<h2>Transfer Items:</h2>
<form autocomplete="off" method="post">
<table class="table">
<thead class="thead-dark">
	<tr>
		<th>ID</th>
		<th>Product</th>
		<th>Variety</th>
		<th>Description</th>
		<th class="r">Sent</th>
		<th class="r">Received</th>
		<th class="r">Price</th>
	</tr>
</thead>
<tbody>
<?php
foreach ($data['Transfer']['inventory_transfer_items'] as $iti) {
?>
	<tr>
		<td>
			<?= $iti['global_inventory_id'] ?><br>
			<small>txn: <?= $iti['global_id'] ?></small>
		</td>
		<td><?= $iti['variety_name'] ?></td>
		<td>
			<?= $iti['description'] ?><br>
			<small>
				<?= $iti['retest'] ? 'RETEST' : '' ?>
				<?php
				if ($iti['is_sample']) {
					if ('lab_sample' == $iti['sample_type']) {
						echo 'Sample / ';
					}
				}
				printf('%s / %s %s'
					, $iti['inventory_type']['type']
					, $iti['inventory_type']['intermediate_type']
					, $iti['is_for_extraction'] ? ' / For Extract' : ''
				);
				?>
			</small>
		</td>
		<td class="r"><?= $iti['qty'] ?></td>
		<td>
			<input name="lot-receive-guid-<?= $iti['global_id'] ?>" type="hidden" value="<?= $iti['global_id'] ?>">
			<input class="form-control form-control-sm r" name="lot-receive-count-<?= $iti['global_id'] ?>" value="<?= $iti['received_qty'] ?: $iti['qty'] ?>">
		</td>
		<td class="r">
			<input class="form-control r" readonly value="<?= $iti['price'] ?>">
		</td>
	</tr>
<?php
}
?>
</tbody>
</table>

<div class="form-group">
	<label>Receive to:</label>
	<select class="form-control" name="section-id">
	<?php
	foreach ($data['Section_list'] as $x) {
		printf('<option value="%s">%s</option>', $x['guid'], $x['name']);
	}
	?>
	</select>
</div>


<div class="form-actions">
	<button class="btn btn-outline-success btn-transfer-accept" disabled><i class="fas fa-check-square"></i> Accept</button>
	<button class="btn btn-outline-danger btn-transfer-accept" disabled><i class="fas fa-ban"></i> Void</button>
</div>

</form>


<script>
$(function() {
	if ('in-transit' == '<?= $data['Transfer']['status'] ?>') {
		$('.btn-transfer-accept').removeAttr('disabled');
	}
});
</script>
