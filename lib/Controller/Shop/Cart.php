<?php
/**
 * Shopping Cart
 */

namespace App\Controller\Shop;

class Cart extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// View
		$data = $this->data;
		$data['product_list'] = $this->load_cart_product_list($_GET['c']);
		$html = $this->render('shop/cart-main.php', $data);
		return $RES->write($html);
	}

	/**
	 *
	 */
	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
			case 'cart-add':

				$c = $_POST['company-id'];
				$i = $_POST['lot-id'];

				$cart = sprintf('cart-%s', $c);

				if (empty($_SESSION[$cart])) {
					$_SESSION[$cart] = [];
				}


				if (empty($_SESSION[$cart][$i])) {
					$_SESSION[$cart][$i] = 1;
				} else {
					$_SESSION[$cart][$i]++;
				}

				__exit_json([
					'data' => [
						'count' => $_SESSION[$cart][$i],
					],
					'meta' => [],
				]);

				break;

			case 'cart-continue':

				$dbc_auth = _dbc('auth');
				$Company = $dbc_auth->fetchRow('SELECT id, name FROM auth_company WHERE id = :c0', [ ':c0' => $_GET['c'] ]);
				if (empty($Company['id'])) {
					// _exit_html_warn('')
					__exit_html('Invalid Request', 400);
				}

				$cart = sprintf('cart-%s', $Company['id']);

				$b2b_sale = [];
				$b2b_sale['id'] = _ulid();
				$b2b_sale['company'] = $Company;
				// $b2b_sale['item_list'] = $_SESSION[$cart];

				$product_want_list = $this->load_cart_product_list($Company['id']);
				foreach ($product_want_list as $p) {

					$b2b_item = [];
					$b2b_item['lot_id'] = $p['lot_id'];
					$b2b_item['product'] = [
						'id' => $p['product_id'],
						'name' => $p['product_name'],
					];
					$b2b_item['variety'] = [
						'id' => $p['variety_id'],
						'name' => $p['variety_name']
					];

					$b2b_sale['item_list'][] = $b2b_item;

				}

				$key = sprintf('b2b-sale-%s', $b2b_sale['id']);
				$_SESSION[$key] = $b2b_sale;

				return $RES->withRedirect(sprintf('/shop/checkout?o=%s', $b2b_sale['id']));

				break;


			case 'cart-delete':

				$key_list = array_keys($_SESSION);
				foreach ($key_list as $k) {
					if (preg_match('/^cart/', $k)) {
						unset($_SESSION[$k]);
					}
				}

				return $RES->withRedirect(sprintf('/shop?c=%s', $_GET['c']));
		}

	}

	/**
	 *
	 */
	function load_cart_product_list($c)
	{
		$dbc_auth = _dbc('auth');
		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $c ]);
		if (empty($Company['id'])) {
			// _exit_html_warn('')
			__exit_html('Invalid Request', 400);
		}

		$dbc_user = _dbc($Company['dsn']);

		$sql = <<<SQL
 SELECT inventory.id AS lot_id,
    inventory.license_id,
    inventory.created_at,
    inventory.guid,
    inventory.stat,
    inventory.flag,
    inventory.qty,
    inventory.qa_cbd AS cbd,
    inventory.qa_thc AS thc,
    COALESCE(inventory.sell, product.sell) AS sell,
    inventory.tags,
    inventory.strain_id AS variety_id,
    strain.name AS variety_name,
    inventory.product_id,
    product.name AS product_name,
    product.package_type,
    product.package_pack_qom,
    product.package_pack_uom,
    product.package_unit_qom,
    product.package_unit_uom,
    product.package_dose_qty,
    product.package_dose_qom,
    product.package_dose_uom,
    product_type.id AS product_type_id,
    product_type.name AS product_type_name,
    product_type.mode AS product_type_mode,
    product_type.unit AS product_type_unit
   FROM inventory
     JOIN strain ON inventory.strain_id::text = strain.id::text
     JOIN product ON inventory.product_id::text = product.id::text
     JOIN product_type ON product.product_type_id::text = product_type.id::text
    WHERE product_type.id NOT IN ('018NY6XC00PR0DUCTTYPE00000', '018NY6XC00PR0DUCTTYPE00001', '018NY6XC00PR0DUCTTYPETY5AT', '018NY6XC00PT8AXVZGNZN3A0QT')
		AND inventory.stat = 200 AND inventory.qty > 0
		AND inventory.id IN ({product_id_list})
	ORDER BY product_type_name, product_name, package_unit_uom
SQL;

		$cart = sprintf('cart-%s', $c);
		$product_want = $_SESSION[$cart];

		$idx = 0;
		$arg = [];
		$tmp = [];
		foreach ($product_want as $p => $q) {
			$idx++;
			$k = sprintf(':pw%d', $idx);
			$tmp[] = $k;
			$arg[$k] = $p;
		}

		$sql = str_replace('{product_id_list}', implode(',', $tmp), $sql);

		$product_list = $dbc_user->fetchAll($sql, $arg);
		return $product_list;

	}
}
