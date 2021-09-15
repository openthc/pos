<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/shop-html.php', APP_ROOT);

?>

<header style="display:flex; justify-content: space-between;">
	<div><h1><?= $data['Page']['title'] ?></h1></div>
	<div style="padding: 0.50rem 0;">
		<a class="btn btn-outline-secondary" href="/shop/cart?c=<?= $data['b2b_sale']['company']['id'] ?>"><i class="fas fa-shopping-cart"></i></a>
	</div>
</header>

<form method="post">
<div class="container">

<!-- List Items -->
<table class="table">
<?php
foreach ($data['b2b_sale']['item_list'] as $idx => $b2b_item) {
	echo '<tr>';
	printf('<td><h3>%d: %s</h3></td>', $idx + 1, $b2b_item['product']['name']);
	printf('<td><h3>%s</h3></td>', $b2b_item['variety']['name']);
	printf('<td class="r"><input class="form-control form-control-ld r" name="product-%s" type="number" min="0" step="1" value="%d">'
		, $b2b_item['lot_id']
		, $b2b_item['qty']
	);
	echo '</tr>';
}
?>
</table>

<hr>

<section>
	<h2>Client Details</h2>

	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<div class="input-group-text" style="width: 6em;">Name:</div>
			</div>
			<input class="form-control form-control-lg" name="contact-name" type="text">
		</div>
	</div>

	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<div class="input-group-text" style="width: 6em;">Email:</div>
			</div>
			<input class="form-control form-control-lg" name="contact-email" type="email">
		</div>
	</div>

	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<div class="input-group-text" style="width: 6em;">Phone:</div>
			</div>
			<input class="form-control form-control-lg" name="contact-phone" type="tel">
		</div>
	</div>

</section>

<div>
	<button class="btn btn-lg btn-primary" name="a" type="submit" value="cart-commit"><i class="fas fa-arrow-right"></i> Continue</button>
	<button class="btn btn-lg btn-outline-secondary" name="a" type="submit" value="cart-update"><i class="fas fa-save"></i> Save</button>
	<button class="btn btn-lg btn-outline-danger" name="a" type="submit" value="cart-delete"><i class="fas fa-trash"></i> Delete</button>
</div>

</div>
</form>
