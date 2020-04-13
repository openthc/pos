{#
// What is all this doing!? This is openting a Hold
<?php
if (!empty($_GET['a']) && ('open-hold' == $_GET['a'])) {

	$arg = array($_GET['hold-id']);
	$SH = SQL::fetch_row('SELECT * FROM b2c_sale_hold WHERE id = ?', $arg);
	$SH['json'] = json_decode($SH['json'], true);

	// Radix::dump($SH);

	echo '<script>';
	echo '$(function() { ';

	foreach ($SH['json'] as $k=>$v) {
		if (preg_match('/^qty-(\d+)$/', $k, $m)) {
			//$
			$I = new Inventory($m[1]);
			$name = substr($I['guid'], -4) . ': ' . $I['name'];
			$size = $SH['json'][sprintf('size-%d', $I['id'])];
			echo "\n Cart_addItem('<div data-id=\"{$I['id']}\" data-name=\"{$name}\" data-price=\"{$I['sell']}\"></div>'); ";
			echo "\n $('#psi-item-{$I['id']}-size').val({$size}); ";
		}
	}

	echo 'chkSaleCost(); ';
	echo '});';
	echo '</script>';
}
-->
#}
