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
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'cart-add':
			return $this->cart_insert($RES);
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
			'data' => [
				'name' => $C['fullname'],
				'rank' => 0,
			],
			'meta' => [ 'detail' => 'success' ]
		]);

	}

}
