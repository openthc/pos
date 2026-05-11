<?php
/**
 * Pick the License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

// __exit_text($data);
?>

<div class="container mt-4">
	<h1>Pick License</h1>

	<?php
	foreach ($data['license_list'] as $lic) {
	?>
		<form method="post">
		<div class="card mb-2">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<h2><code><?= __h($lic['code']) ?></code> <?= __h($lic['name']) ?></h2>
					<button class="btn btn-outline-primary btn-license-pick"
						data-license-id="<?= __h($lic['id']) ?>"
						name="a"
						value="license-select">
						<i class="fa-regular fa-square-check"></i> Open
					</button>
					<input name="license-id" type="hidden" value="<?= __h($lic['id']) ?>">
				</div>
			</div>
		</div>
		</form>
	<?php
	}
	?>

</div>


<script>
$(function() {
	$('.btn-license-pick').on('click', function() {
		const lid = this.getAttribute('data-license-id');
		localStorage.setItem('license-select', lid);
	});

	const chk = localStorage.getItem('license-select');
	if (chk) {
		// Find the Matching Button
		debugger;
		$(`.btn-license-pick[data-license-id="${chk}"]`).addClass('btn-primary').removeClass('btn-outline-primary');
	}
})

</script>
