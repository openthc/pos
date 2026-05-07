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
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
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

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $b2c_cart['id']);

		$ret = [
			'data' => $Cart,
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
		$b2c_cart = json_decode($b2c_cart);

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $b2c_cart->id);

		foreach ($b2c_cart->item_list as $b2c_item) {

			$b2c_item = new \OpenTHC\POS\Cart\Item($b2c_item);

			if ($b2c_item->unit_count <= 0) {
				$Cart->delItem($b2c_item->id);
				continue;
			}

			$Cart->addItem($b2c_item);

		}

		$Cart->save();

		$ret = [
			'data' => $Cart,
			'meta' => [],
		];

		// Now Create the HTML for the Whole Cart
		// if (is_htmx()) {
		// }

		return $RES->withJSON($ret);

	}
}
