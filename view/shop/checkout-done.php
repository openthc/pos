<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/shop-html.php', APP_ROOT);

?>

<header style="display:flex; justify-content: space-between;">
	<div><h1><?= $data['Page']['title'] ?></h1></div>
	<div>
		<h2 class="badge bg-success">Complete</h2>
	</div>
</header>

<div class="container">
	<div class="alert alert-success">Order Complete! You will receive an email or phone confirmation shortly.</div>
</div>

<form method="post">
<div class="container">

<!-- List Items -->
<table class="table">
<?php
foreach ($data['b2c']['item_list'] as $idx => $b2b_item) {
	echo '<tr>';
	printf('<td><h3>%d: %s</h3></td>', $idx + 1, $b2b_item['product']['name']);
	printf('<td><h3>%s</h3></td>', $b2b_item['variety']['name']);
	printf('<td class="r"><input class="form-control form-control-ld r" type="number" min="0" step="1" value="%d">'
		, $b2b_item['qty']
	);
	echo '</tr>';
}
?>
</table>

<hr>

<section>
	<h2>Client Details</h2>

	<div class="mb-2">
		<div class="input-group">
			<div class="input-group-text" style="width: 6em;">Name:</div>
			<input class="form-control form-control-lg" readonly type="text" value="<?= __h($data['b2c']['contact']['name']) ?>">
		</div>
	</div>

	<div class="mb-2">
		<div class="input-group">
			<div class="input-group-text" style="width: 6em;">Email:</div>
			<input class="form-control form-control-lg" readonly type="email" value="<?= __h($data['b2c']['contact']['email']) ?>">
		</div>
	</div>

	<div class="mb-2">
		<div class="input-group">
			<div class="input-group-text" style="width: 6em;">Phone:</div>
			<input class="form-control form-control-lg" readonly type="tel" value="<?= __h($data['b2c']['contact']['phone']) ?>">
		</div>
	</div>

</section>

<div>
	<a class="btn btn-lg btn-primary" href="/shop?c=<?= $data['b2c']['company']['id'] ?>"><i class="fas fa-store"></i> Shop Storefront</a>
	<button class="btn btn-lg btn-outline-danger" name="a" type="submit" value="b2c-sale-delete"><i class="fas fa-trash"></i> Delete</button>
</div>

</div>
</form>
