<?php
/**
 * Modal for Loyalty Program
 *
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * @todo Maybe just one input box for any/all codes?
 */

$body = <<<HTML
<div class="row">
	<div class="col-md-6">
		<h5>Code / ID</h5>
		<p>Their Loyalty ID or scanned ID card</p>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control form-control-lg" id="loyalty-other" type="text">
			<button class="btn btn-secondary" type="button"><i class="fas fa-qrcode"></i></button>
		</div>
	</div>
</div>

<div id="pos-modal-loyalty-list">
	<i class="fas fa-sync fa-spin"></i> Loading...
</div>

HTML;

$foot = <<<HTML
<button class="btn btn-lg btn-primary" id="pos-loyalty-apply" name="a" type="submit" value="pos-loyalty-apply"><i class="fas fa-check-square"></i> Apply</button>
HTML;


echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-loyalty',
	'modal_title' => 'Checkout :: Loyalty',
	'body' => $body,
	'foot' => $foot,
]);
