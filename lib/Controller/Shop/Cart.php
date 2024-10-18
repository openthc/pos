<?php
/**
 * Shopping Cart
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Shop;

use Edoceo\Radix\ULID;

class Cart extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// View
		$data = $this->data;
		$data['Page']['title'] = 'Cart';
		$data['Company'] = [
			'id' => $_GET['c'],
		];

		$cart = sprintf('cart-%s', $data['Company']['id']);

		$product_list = $this->load_cart_product_list($data['Company']['id']);

		$data['product_list'] = [];
		foreach ($product_list as $p) {
			$data['product_list'][] = [
				'inventory' => [
					'id' => $p['inventory_id']
				]
				, 'product' => [
					'id' => $p['product_id']
					, 'name' => $p['product_name']
					, 'sell' => $p['sell']
				]
				, 'variety' => $p['variety']
				, 'qty' => $_SESSION[$cart][ $p['inventory_id'] ]
			];
		}

		$html = $this->render('shop/cart.php', $data);

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
				$i = $_POST['inventory-id'];

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
					_exit_html_warn('Invalid Request [CSC-064]', 400);
				}

				$cart = sprintf('cart-%s', $Company['id']);

				$b2b_sale = [];
				$b2b_sale['id'] = ULID::create();
				$b2b_sale['company'] = $Company;

				$product_want_list = $this->load_cart_product_list($Company['id']);
				foreach ($product_want_list as $p) {

					$b2b_item = [];
					$b2b_item['qty'] = $_SESSION[$cart][$p['inventory_id']];
					$b2b_item['inventory'] = [
						'id' => $p['inventory_id']
					];
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

				\Edoceo\Radix\Session::flash('info', 'Cart has been emptied');

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
			_exit_html_fail('Invalid Request [CSC-121]', 400);
		}

		$dbc_user = _dbc($Company['dsn']);

		$sql = <<<SQL
		SELECT inventory.id AS inventory_id,
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
			inventory.variety_id AS variety_id,
			variety.name AS variety_name,
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
			JOIN variety ON inventory.variety_id::text = variety.id::text
			JOIN product ON inventory.product_id::text = product.id::text
			JOIN product_type ON product.product_type_id::text = product_type.id::text
			WHERE product_type.id NOT IN ('018NY6XC00PR0DUCTTYPE00000', '018NY6XC00PR0DUCTTYPE00001', '018NY6XC00PR0DUCTTYPETY5AT', '018NY6XC00PT8AXVZGNZN3A0QT')
				AND inventory.stat = 200 AND inventory.qty > 0
				AND inventory.id IN ({product_id_list})
			ORDER BY product_type_name, product_name, package_unit_uom
		SQL;

		$cart = sprintf('cart-%s', $c);
		$product_want = $_SESSION[$cart];
		if (empty($product_want)) {
			return [];
		}

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
