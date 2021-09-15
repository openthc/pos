<?php
/**
 *
 */

$this->layout_file = sprintf('%s/view/_layout/shop-html.php', APP_ROOT);

?>

<header style="display:flex; justify-content: space-between;">
	<div><h1><?= $data['Page']['title'] ?></h1></div>
	<div style="padding: 0.50rem 0;">
		<a class="btn btn-outline-primary" href="/shop?c=<?= $data['Company']['id'] ?>"><i class="fas fa-store"></i> Storefront</a>
		<!-- <a class="btn btn-outline-secondary" href="/shop/cart"><i class="fas fa-shopping-cart"></i></a> -->
	</div>
</header>

<form method="post">
<div>
	<button class="btn btn-lg btn-primary" name="a" type="submit" value="cart-continue"><i class="fas fa-arrow-right"></i> Continue</button>
	<button class="btn btn-lg btn-outline-secondary" name="a" type="submit" value="cart-update"><i class="fas fa-save"></i> Save</button>
	<button class="btn btn-lg btn-outline-danger" name="a" type="submit" value="cart-delete"><i class="fas fa-trash"></i> Delete</button>
</div>

<div class="product-grid">
<?php
foreach ($data['product_list'] as $p) {
?>
	<div class="product-item-wrap">
		<div class="product-item">
			<header><h2><?= __h($p['product']['name']) ?></h2></header>
			<div class="img-fluid c"><img src="https://cdn.openthc.com/img/icon/icon-256.png"></div>
			<div class="product-item-foot">
				<div class="product-cost">$<?= $p['product']['sell'] ?></div>
				<div>
					<input
						class="form-control form-control-lg r"
						data-lot-id="<?= $p['lot']['id'] ?>"
						data-product-id="<?= $p['product']['id'] ?>"
						value="<?= $p['qty'] ?>"
					>
				</div>
			</div>
		</div>
	</div>
<?php
}
?>
</div>

<div>
	<button class="btn btn-lg btn-primary" name="a" type="submit" value="cart-continue"><i class="fas fa-arrow-right"></i> Continue</button>
	<button class="btn btn-lg btn-outline-secondary" name="a" type="submit" value="cart-update"><i class="fas fa-save"></i> Save</button>
	<button class="btn btn-lg btn-outline-danger" name="a" type="submit" value="cart-delete"><i class="fas fa-trash"></i> Delete</button>
</div>

</form>
