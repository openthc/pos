<?php
/**
 * A Bootstrap Modal
 */

?>

<div class="modal" id="<?= $data['modal_id'] ?>" role="dialog" tabindex="-1">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">

<div class="modal-header">
	<h4 class="modal-title"><?= $data['modal_title'] ?></h4>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>

<div class="modal-body"><?= $data['body'] ?></div>

<div class="modal-footer">
<div class="d-flex" style="width: 100%;">
	<div style="flex: 1 1 auto;">
		<button class="btn btn-outline-secondary" data-dismiss="modal" taborder="-1" type="button"><i class="fas fa-times"></i> Cancel</button>
	</div>
	<div class="r" style="flex: 1 1 auto;"><?= $data['foot'] ?></div>
</div>
</div>

</div>
</div>
</div>
