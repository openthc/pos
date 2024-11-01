<?php
/**
 * Cart Ajax Helper
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Cart;

use Edoceo\Radix;
use Edoceo\Radix\Session;
use Edoceo\Radix\ULID;

use OpenTHC\Contact;

class Ajax extends \OpenTHC\Controller\Base
{
	protected $tax_info;

	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// Load Tax Data
		$this->initTaxData();

		switch ($_POST['a']) {
		case 'cart-reload':
			return $this->show_current_state($REQ, $RES);
		case 'cart-update':
		case 'update':
			return $this->update($REQ, $RES);
		case 'cart-option-save':

			$dbc = $this->_container->DB;

			// Do Something

			return $RES->withJSON([
				'data' => null,
				'meta' => [ 'note' => 'Not Implemented [PCA-028]' ],
			], 501);

		case 'loyalty':

			$dbc = $this->_container->DB;

			if (!empty($_POST['phone'])) {

				//$x = _phone_e164($_POST['phone']);

				$x = preg_replace('/[^\d]+/', null, $_POST['phone']);
				$sql = 'SELECT * FROM contact WHERE phone = ?';
				$arg = array($x);
				$chk = $dbc->fetchRow($sql, $arg);
				if (empty($C)) {
					$C = new Contact($dbc);
					$C['id'] = ULID::create();
					$C['guid'] = $C['id'];
					$C['fullname'] = $x;
					$C['phone'] = $x;
					$C->setFlag(Contact::FLAG_B2C_CLIENT);
					$C['hash'] = $C->getHash();
					$C->save();
				} else {
					$C = new Contact($dbc, $chk);
				}
				break;
			} elseif (!empty($_POST['email'])) {
				$x = trim(strtolower($_POST['email']));
				$sql = 'SELECT * FROM contact WHERE email = ?';
				$arg = array($x);
				$chk = $dbc->fetchRow($sql, $arg);
				if (empty($C)) {
					$C = new Contact($dbc);
					$C['id'] = ULID::create();
					$C['guid'] = $C['id'];
					$C['fullname'] = $x;
					$C['email'] = $x;
					$C->setFlag(Contact::FLAG_B2C_CLIENT);
					$C['hash'] = $C->getHash();
					$C->save();
				} else {
					$C = new Contact($dbc, $chk);
				}
				break;
			} elseif (!empty($_POST['other'])) {
				$x = trim(strtolower($_POST['other']));
				$sql = 'SELECT * FROM contact WHERE altid = ?';
				$arg = array($x);
				$chk = $dbc->fetchRow($sql, $arg);
				if (empty($C)) {
					$C = new Contact($dbc);
					$C['id'] = ULID::create();
					$C['guid'] = $C['id'];
					$C['fullname'] = $x;
					$C['altid'] = $x;
					$C->setFlag(Contact::FLAG_B2C_CLIENT);
					$C['hash'] = $C->getHash();
					$C->save();
				} else {
					$C = new Contact($dbc, $chk);
				}
			}
		}

		return $RES->withJSON([
			'data' => null,
			'meta' => [ 'note' => 'Invalid Request' ]
		], 400);

	}

	function show_current_state($REQ, $RES)
	{
		$b2c_cart = $_POST['cart'];
		$b2c_cart = json_decode($b2c_cart, true);

		$key = sprintf('/%s/cart/%s', $_SESSION['License']['id'], $b2c_cart['id']);

		$rdb = $this->_container->Redis;
		$Cart = $rdb->get($key);
		if ( ! empty($Cart)) {
			$Cart = json_decode($Cart);
		} else {
			$Cart = [
				'item_list' => [],
				'item_count' => 0,
				'item_',
			];
		}

		$ret = [
			'data' => [
				'Cart' => $Cart,
			],
			'meta' => [],
		];

		return $RES->withJSON($ret);

	}

	/**
	 *
	 */
	function update($REQ, $RES)
	{
		$rdb = $this->_container->Redis;

		$b2c_cart = $_POST['cart'];
		$b2c_cart = json_decode($b2c_cart, true);

		// $b2c_item_list = $_POST['item_list'];
		// $b2c_item_list = json_decode($b2c_item_list, true);

		$key = sprintf('/%s/cart/%s', $_SESSION['License']['id'], $b2c_cart['id']);
		$Cart = $rdb->get($key);
		if ( ! empty($Cart)) {
			$Cart = json_decode($Cart, true);
		} else {
			$Cart = [];
			$Cart['id'] = $b2c_cart['id'];
			$Cart['key'] = sprintf('/%s/cart/%s', $_SESSION['License']['id'], $Cart['id']);
			$Cart['item_list'] = [];
		};

		// Reset Totals
		$Cart['item_list'] = [];
		$Cart['item_count']       = 0;
		$Cart['unit_count']       = 0;
		$Cart['unit_price_total'] = 0;
		// $Cart['unit_price'] = 0;
		$Cart['tax_total']        = 0;
		$Cart['full_price']       = 0;

		foreach ($b2c_cart['item_list'] as $b2c_item) {

			if ($b2c_item['unit_count'] <= 0) {
				continue;
			}

			$b2c_item['unit_price_total'] = $b2c_item['unit_price'] * $b2c_item['unit_count'];

			// Add Taxes
			$b2c_item['tax_list'] = [];
			$b2c_item['tax_total'] = 0;
			foreach ($this->tax_info->tax_list as $tax_ulid => $tax_rate) {
				if ( ! empty($tax_rate)) {
					if ($tax_rate > 0) {
						$tax_rate = $tax_rate / 100;
					}
					$tax_cost = $b2c_item['unit_price_total'] * $tax_rate;
					$b2c_item['tax_list'][$tax_ulid] = $tax_cost;
					$b2c_item['tax_total'] += $tax_cost;
				}
			}
			$b2c_item['full_price'] = $b2c_item['unit_price_total'] + $b2c_item['tax_total'];

			// Format
			$b2c_item['unit_price'] = sprintf('%0.2f', $b2c_item['unit_price']);
			$b2c_item['unit_price_total'] = sprintf('%0.2f', $b2c_item['unit_price_total']);
			$b2c_item['full_price'] = sprintf('%0.2f', $b2c_item['full_price']);

			// Set in Cart
			$Cart['item_list'][ $b2c_item['id'] ] = $b2c_item;

			// Update Cart Totals
			$Cart['item_count'] += 1;
			$Cart['unit_count'] += $b2c_item['unit_count'];
			$Cart['unit_price_total'] += $b2c_item['unit_price_total'];
			// $Cart['unit_price']
			$Cart['tax_total']  += array_sum($b2c_item['tax_list']);
			$Cart['full_price'] += $b2c_item['full_price'];

		}

		$Cart['unit_price_total'] = sprintf('%0.2f', $Cart['unit_price_total']);
		$Cart['tax_total']  = sprintf('%0.2f', $Cart['tax_total']);
		$Cart['full_price'] = sprintf('%0.2f', $Cart['full_price']);

		$rdb->set($Cart['key'], json_encode($Cart), [ 'ex' => '43200' ]);

		$ret = [
			'data' => [
				'Cart' => $Cart,
			],
			'meta' => [],
		];

		// $b2c_item = $_POST['item'];
		// $b2c_item['unit_price'] = $b2c_item['price'];
		// $b2c_item['unit_price_total'] = $b2c_item['unit_price'] * $b2c_item['qty'];

		// Now Create the HTML for the Whole Cart

		// Store Cart w/ID, Update and Send Back?
		// $k = sprintf('pos-terminal-card', $_SESSION['pos-terminal-id']);
		// $this->_container->Redis->del($k);
		// $x = $this->_container->Redis->set($k, json_encode($_POST));

		return $RES->withJSON($ret);

	}

	/**
	 *
	 */
	private function initTaxData() : void
	{
		// Load Tax Data
		$rdb = $this->_container->Redis;
		$key = sprintf('/%s/pos/b2c/item/adjust-list', $_SESSION['License']['id']);
		$tax_info = $rdb->get($key);
		if ( ! empty($tax_info)) {
			$this->tax_info = json_decode($tax_info);
			return;
		}

		$dbc = $this->_container->DB;

		$Company = new \OpenTHC\Company($dbc, $_SESSION['Company']);
		$License = new \OpenTHC\License($dbc, $_SESSION['License']['id']);

		$this->tax_info = new \stdClass();
		$this->tax_info->tax_incl = $Company->getOption(sprintf('/%s/b2c-item-price-adjust/tax-included', $License['id']));
		$this->tax_info->tax_list = new \stdClass();

		$this->tax_info->tax_list->{'010PENTHC00BIPA0SST03Q484J'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0SST03Q484J', $License['id']))); // State
		$this->tax_info->tax_list->{'010PENTHC00BIPA0C0T620S2M2'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0C0T620S2M2', $License['id']))); // County
		$this->tax_info->tax_list->{'010PENTHC00BIPA0CIT5H9S6T3'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0CIT5H9S6T3', $License['id']))); // City
		$this->tax_info->tax_list->{'010PENTHC00BIPA0MUT0FEEGCF'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0MUT0FEEGCF', $License['id']))); // Regional
		$this->tax_info->tax_list->{'010PENTHC00BIPA0ET0FNBCKMH'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0ET0FNBCKMH', $License['id']))); // Excise

		$rdb->set($key, json_encode($this->tax_info), [ 'ex' => '1800' ]);

	}

}
