<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/shop-html.php', APP_ROOT);

?>

<header style="display:flex; justify-content: space-between;">
	<div><h1><?= $data['Page']['title'] ?></h1></div>
	<div style="padding: 0.50rem 0;">
		<a class="btn btn-primary" href="/shop/cart?c=<?= $data['Company']['id'] ?>"><i class="fas fa-shopping-cart"></i> View Cart</a>
	</div>
</header>

<div class="product-grid">
<?php
foreach ($data['product_list'] as $p) {
?>
	<div class="product-item-wrap">
		<div class="product-item">
			<header>
				<h2><?= __h($p['product_name']) ?></h2>
				<h3><?= __h($p['variety_name']) ?></h3>
			</header>
			<div class="img-fluid c"><img src="https://cdn.openthc.com/img/icon/icon-256.png"></div>
			<div class="product-item-foot">
				<div class="product-cost"><?= $p['sell'] ?></div>
				<div>
					<button
						class="btn btn-outline-success btn-product-add"
						data-inventory-id="<?= $p['inventory_id'] ?>"
						data-product-id="<?= $p['product_id'] ?>">
							<i class="fas fa-cart-plus"></i> Add
					</button>
				</div>
			</div>
			<div style="background: #dddddd; padding: 0 0.25rem;">
				Inventory: <span style="font-family: monospace; font-size: 80%;"><?= $p['id'] ?></span>
			</div>
		</div>
	</div>
<?php
}
// var_dump($data);
?>
</div>


<script>
$(function() {
	$('.btn-product-add').on('click', function() {

		var btn0 = this;

		var form = new FormData();
		form.set('a', 'cart-add');
		form.set('company-id', '<?= $_GET['c'] ?>');
		form.set('inventory-id', btn0.dataset.inventoryId);
		form.set('product-id', btn0.dataset.productId);
		form.set('qty', 1);

		fetch('/shop/cart', {
			method: 'POST',
			body: form
		}).then(res => res.json()).then(json => {
			// console.log(json);

			btn0.innerHTML = `<i class="fas fa-cart-plus"></i> ${json.data.count}`;

		});

	});
});
</script>
