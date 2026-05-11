<?php
/**
 * POS Configuration
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

?>


<h1><?= __h($_SESSION['License']['name']) ?> :: <code><?= __h($_SESSION['License']['code']) ?></code></h1>

<div class="container">

	<form method="post">
	<section>
		<div class="card">
			<div class="card-header fs-2">Receipt Printer Print Queue</div>
			<div class="card-body">
				<div class="input-group mb-2">
					<div class="input-group-text">Print Queue ID:</div>
					<input class="form-control" name="print-queue-code" value="<?= $data['receipt-queue-id'] ?>">
				</div>
				<div class="input-group mb-2">
					<div class="input-group-text">Printer Name:</div>
					<input class="form-control" name="printer-name" value="<?= $data['receipt-printer-name'] ?>">
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" name="a" value="print-queue-receipt-update"><i class="fas fa-save"></i> Save</button>
				<a class="btn btn-secondary" href="?a=download-script&amp;s=receipt"><i class="fas fa-download"></i> Download</a>
			</div>
		</div>
	</section>
	</form>

	<hr>

	<form method="post">
	<section>
		<div class="card">
			<div class="card-header fs-2">Pick Ticket Print Queue</div>
			<div class="card-body">
				<div class="input-group mb-2">
					<div class="input-group-text">Print Queue ID:</div>
					<input class="form-control" name="print-queue-code" value="<?= $data['pick-ticket-queue-id'] ?>">
				</div>
				<div class="input-group mb-2">
					<div class="input-group-text">Printer Name:</div>
					<input class="form-control" name="printer-name" value="<?= $data['pick-ticket-printer-name'] ?>">
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" name="a" value="print-queue-pick-ticket-update"><i class="fas fa-save"></i> Save</button>
				<a class="btn btn-secondary" href="?a=download-script&amp;s=pick-ticket"><i class="fas fa-download"></i> Download</a>
			</div>
		</div>
	</section>
	</form>

	<a href="/test">Test Mode Stuff</a>

</div>
