<?php
/**
 * The ID Scanner Modal
 */

$body = <<<HTML
<div id="scan-input-stat"></div>
<div id="scan-input-data"></div>
HTML;

$foot = <<<HTML
<button class="btn btn-outline-primary" disabled name="a" type="button"><i class="fas fa-check-square"></i> Complete</button>
HTML;

echo $this->block('modal.php', [
	'modal_id' => 'pos-modal-scan-id',
	'modal_title' => 'Scan ID',
	'body' => $body,
	'foot' => $foot,
]);
