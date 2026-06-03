<?php
/**
 * POS Configuration
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

?>


<h1><?= __h($_SESSION['License']['name']) ?> :: <code><?= __h($_SESSION['License']['code']) ?></code></h1>

<div class="container">

<div class="row">
	<div class="col-lg-6 mb-2">
		<form method="post">
		<section>
			<div class="card">
				<div class="card-header fs-2">Scanner</div>
				<div class="card-body">
					<div class="input-group mb-2">
						<select class="form-select" name="scanner-mode">
							<option value="0">- Disabled -</option>
							<option value="1">Enabled</option>
							<option value="1">Enabled - Camera</option>
						</select>
					</div>
					<div class="input-group mb-2">
						<div class="input-group-text">Device ID:</div>
						<input class="form-control" name="scanner-device-id" value="<?= $data['scanner-device-id'] ?>">
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary" name="a" value="print-queue-receipt-update"><i class="fas fa-save"></i> Save</button>
				</div>
			</div>
		</section>
		</form>
	</div>
</div> <!-- /.row -->

	<div class="row">

	<div class="col-lg-6">
		<form method="post">
		<section>
			<div class="card">
				<div class="card-header fs-2">Receipt Printer Print Queue</div>
				<div class="card-body">
					<div class="input-group mb-2">
						<div class="input-group-text">Print Queue:</div>
						<select class="form-select" name="print-queue-id">
							<option value="">- Disabled -</option>
							<?php
							foreach ($data['print_queue_list'] as $pq0) {
								$sel = ' ';
								if ($data['receipt-queue-id'] == $pq0->id) {
									$sel = ' selected ';
								}
							?>
								<option <?= $sel ?> value="<?= $pq0->id ?>"><?= __h($pq0->{'queue-name'}) ?></option>
							<?php
							}
							?>
						</select>
					</div>
					<div class="input-group mb-2">
						<div class="input-group-text">Print Queue ID:</div>
						<input class="form-control disabled" disabled readonly value="<?= $data['receipt-queue-id'] ?>">
					</div>
					<div class="input-group mb-2">
						<div class="input-group-text">Printer Name:</div>
						<input class="form-control disabled" disabled readonly value="<?= $data['receipt-printer-name'] ?>">
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary" name="a" value="print-queue-receipt-update"><i class="fas fa-save"></i> Save</button>
					<a class="btn btn-secondary" href="?a=download-script&amp;s=receipt"><i class="fas fa-download"></i> Download</a>
				</div>
			</div>
		</section>
		</form>
	</div>

	<div class="col-lg-6">
		<form method="post">
		<section>
			<div class="card">
				<div class="card-header fs-2">Pick Ticket Print Queue</div>
				<div class="card-body">
					<div class="input-group mb-2">
						<div class="input-group-text">Print Queue:</div>
						<select class="form-select" name="print-queue-id">
							<option value="">- Disabled -</option>
							<?php
							foreach ($data['print_queue_list'] as $pq0) {
								$sel = ' ';
								if ($data['pick-ticket-queue-id'] == $pq0->id) {
									$sel = ' selected ';
								}
							?>
								<option <?= $sel ?> value="<?= $pq0->id ?>"><?= __h($pq0->{'queue-name'}) ?></option>
							<?php
							}
							?>
						</select>
					</div>
					<div class="input-group mb-2">
						<div class="input-group-text">Print Queue ID:</div>
						<input class="form-control" disabled readonly value="<?= $data['pick-ticket-queue-id'] ?>">
					</div>
					<div class="input-group mb-2">
						<div class="input-group-text">Printer Name:</div>
						<input class="form-control" disabled readonly value="<?= $data['pick-ticket-printer-name'] ?>">
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary" name="a" value="print-queue-pick-ticket-update"><i class="fas fa-save"></i> Save</button>
					<a class="btn btn-secondary" href="?a=download-script&amp;s=pick-ticket"><i class="fas fa-download"></i> Download</a>
				</div>
			</div>
		</section>
		</form>
	</div>

	<a href="/test">Test Mode Stuff</a>

</div>
</div>
