
<style>
#receipt-preview-wrap iframe {
	border: none;
	height: 50vh;
	margin: 0;
	padding: 0;
	width: 100%;
}
</style>


<div class="container">

	<div class="hero">
		<h1>Receipt Text</h1>
	</div>

<div class="row">
	<div class="col-md-6">

		<h2>Editor</h2>

		<div class="form-group">
			<label>Header</label>
			<input class="form-control" name="pos-receipt-head" value="<?= __h($data['receipt-head']) ?>">
			Maybe allow an image?
		</div>

		<textarea class="form-control" name="pos-receipt-tail" style="height: 40vh;"><?= __h($data['receipt-tail']) ?></textarea>
		<textarea class="form-control" name="pos-receipt-foot" style="height: 40vh;"><?= __h($data['receipt-foot']) ?></textarea>

	</div>
	<div class="col-md-6">
		<h2>Preview <button class="btn btn-sm btn-outline-secondary btn-print-preview" type="button"><i class="fas fa-print"></i></button></h2>
		<div id="receipt-preview-wrap"></div>
		<img src="">
	</div>
</div>



</div>


<script>
$(function() {
	$('.btn-print-preview').on('click', function() {
		// Render Some PDF in an iFrame Maybe?
		$('#receipt-preview-wrap').html('<iframe src="/settings/receipt/preview"></iframe>');
	});
});
</script>
