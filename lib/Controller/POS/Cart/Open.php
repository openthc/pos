<?php
/**
 * Given an ID respond with JavaScript that adds those items to the Ticket
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Cart;

use Edoceo\Radix;
use Edoceo\Radix\Session;

class Open extends \OpenTHC\Controller\Base
{
	use \OpenTHC\POS\Feature\LoadTaxRateInfo;

	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		if ( ! empty($_GET['hold'])) {

			$dbc = $this->_container->DB;

			// Open a Hold
			$arg = [];
			$arg[':bsh0'] = $_GET['hold'];

			$rec = $dbc->fetchRow('SELECT * FROM b2c_sale_hold WHERE id = :bsh0', $arg);
			$raw_cart = json_decode($rec['meta'], true);

			$version = ($raw_cart['@version'] ?: 'v0');
			switch ($version) {
				case 'v0':

					$Cart = new \OpenTHC\POS\Cart($this->_container->Redis);
					$Cart->type = 'REC'; // $_POST['pos-cart-type'];
					$Cart->setTaxRateData( $this->loadTaxRateInfo() );
					$Cart->Contact = [
						'id' => '018NY6XC00C0NTACT000WALK1N',
						'stat' => 200,
						'name' => 'Walk In',
					];

					foreach ($raw_cart['item_list'] as $src_item) {

						// new \OpenTHC\POS\Inventory($dbc, $src_item['inventory_id']);
						$Inv = $dbc->fetchRow('SELECT * FROM inventory_full WHERE license_id = :l0 AND id = :i0', [
							':l0' => $_SESSION['License']['id'],
							':i0' => $src_item['inventory_id'],
						]);
						if (empty($Inv['id'])) {
							throw new \Exception('Invalid Request [PCO-051]', 500);
						}

						$Inv['name'] = sprintf('%s / %s', $Inv['product_name'], $Inv['variety_name']);
						$Inv['name'] = trim($Inv['name'], '/');

						$b2c_item = new \stdClass();
						$b2c_item->id = $Inv['id'];
						// $b2c_item->type = 'REC';
						$b2c_item->name = substr($Inv['guid'], -4) . ': ' . ($Inv['name']);
						$b2c_item->unit_count = $src_item['unit_count'];
						$b2c_item->unit_price = $Inv['sell'];
						// $b2c_item->unit_weight = $Inv['product']['package]['unit_weight']

						$b2c_item = new \OpenTHC\POS\Cart\Item($b2c_item);
						$Cart->addItem($b2c_item);
					}
					$Cart->save();

					$url = sprintf('/pos?cart=%s', $Cart->id);

					return $RES->withRedirect($url);

					break;
				case 'v1':
				case 'v2026':

			}

			foreach ($b2c_cart->item_list as $b2c_item) {

				$b2c_item = new \OpenTHC\POS\Cart\Item($b2c_item);

				if ($b2c_item->unit_count <= 0) {
					$Cart->delItem($b2c_item->id);
					continue;
				}

				$Cart->addItem($b2c_item);

			}


		}
	}
}
