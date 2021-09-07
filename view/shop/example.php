<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/shop-html.php', APP_ROOT);

?>

<header style="display:flex; justify-content: space-between;">
	<div><h1><?= $data['Page']['title'] ?></h1></div>
	<div>
		<a class="btn btn-outline-secondary" href="/shop/cart?c=<?= $data['Company']['id'] ?>"><i class="fas fa-shopping-cart"></i></a>
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
						data-lot-id="<?= $p['lot_id'] ?>"
						data-product-id="<?= $p['product_id'] ?>">
							<i class="fas fa-cart-plus"></i> Add
					</button>
				</div>
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
		form.set('lot-id', btn0.dataset.lotId);
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
