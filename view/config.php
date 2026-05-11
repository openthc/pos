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
				<input class="form-control" name="print-queue-code" value="<?= $data['receipt-queue-id'] ?>">
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" name="a" value="print-queue-receipt-update"><i class="fas fa-save"></i> Save</button>
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
				<input class="form-control" name="print-queue-code" value="<?= $data['pick-ticket-queue-id'] ?>">
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" name="a" value="print-queue-pick-ticket-update"><i class="fas fa-save"></i> Save</button>
			</div>
		</div>
	</section>
	</form>

	<a href="/test">Test Mode Stuff</a>

</div>
