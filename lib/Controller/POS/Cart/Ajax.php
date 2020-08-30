<?php
/**
 * Cart Ajax Helper
 */

namespace App\Controller\POS\Cart;

use Edoceo\Radix;
use Edoceo\Radix\Session;
use Edoceo\Radix\DB\SQL;

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
					$C['id'] = _ulid();
					$C['guid'] = $C['id'];
					$C['ulid'] = $C['id'];
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
					$C['id'] = _ulid();
					$C['ulid'] = $C['id'];
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
					$C['id'] = _ulid();
					$C['ulid'] = $C['id'];
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
/*
switch ($_POST['a']) {
	case 'dispense':
		$s['data'] = json_encode(array(
			'barcodeid' => $_POST['barcodeid'],
			'quantity' => $_POST['quantity'],
			'price' => $_POST['price']
		));
		break;

	case 'void':
		$s['transactionid'] = $_POST['transactionid'];
		break;

	case 'modify':
		$s['transactionid'] = $_POST['transactionid'];
		$s['barcodeid'] = $_POST['barcodeid'];
		$s['price'] = $_POST['price'];
		$s['item_number'] = $_POST['item_number'];
		break;

	case 'refund':
		$s['data'] = json_encode(array(
			'barcodeid' => $_POST['barcodeid'],
			'quantity' => $_POST['quantity'],
			'price' => $_POST['price']

		));
		break;
}
*/

		return $RES->withJSON([
			'data' => [
				'name' => $C['fullname'],
				'rank' => 0,
			],
			'meta' => [ 'detail' => 'success' ]
		]);

	}

	private function insert_cart($RES)
	{
		if (empty($_SESSION['cart'])) $_SESSION['cart'] = new Radix_Cart();

		if (empty($_POST['inventory_id'])) {
			Session::flash('warn', 'An Inventory Item is required to add to a Sales process');
			return(0);
		}

		$I = new Inventory($_POST['inventory_id']);
		if (empty($I['id'])) {
			Session::flash('fail', 'Inventory Item not found');
			return(0);
		}

		$_SESSION['cart']->add(array(
			'name' => $I['strain'],
			'size' => $_POST['size'],
			'cost' => $I['sell'],
		));

	}
}
