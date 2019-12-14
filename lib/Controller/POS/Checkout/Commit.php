<?php
/**
 * POS Checkout Commit
*/

namespace App\Controller\POS\Checkout;

use Edoceo\Radix\Session;
use Edoceo\Radix\DB\SQL;

class Commit extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		//_bill_plan_check();
		//_post_license_filter();

		var_dump($_POST);
		var_dump($_SESSION);

		//$License = new \OpenTHC\License($_POST['license_id']);
		$License = new \OpenTHC\License($_SESSION['License']['id']);
		var_dump($License);

		try {
			$dbc->query('BEGIN');

			$Sale = new \App\B2C\Sale();
			$Sale['uid'] = 2; // $_SESSION['uid'];
			$Sale['license_id'] = $License['id'];
			$Sale['bill_sub'] = $_POST['sub'];
			$Sale['bill_tax_i502'] = $_POST['tax_i502'];
			$Sale['bill_tax_sale'] = $_POST['tax_sale'];
			$Sale['bill_due'] = $_POST['due'];
			$Sale['bill_pay'] = $_POST['pay'];
			$Sale['meta'] = json_encode($_POST);
			$Sale->save();

			$key_list = array_keys($_POST);
			foreach ($key_list as $key) {

				if (preg_match('/^qty\-(\d+)/', $key, $m)) {

					$qty = floatval($_POST[$key]);
					$IL = new \App\Lot($m[1]);

					$SI = new \App\B2C\Sale\Item();
					$SI['b2c_sale_id'] = $Sale['id'];
					$SI['inventory_id'] = $IL['id'];
					$SI['qty'] = $qty;
					$SI['qom'] = 0; // $IL['package_size'];
					$SI['unit_price']= $IL['sell'];
					$SI['uom'] = 'ea';
					$SI->save();

					$IL->decrement($qty);

				}
			}

			$dbc->query('COMMIT');

		} catch (Exception $e) {
			_exit_text('Failed to Execute the Sale', 500);
		}

		//$cre = RCE::factory();
		// _exec_sale_in_cre($Sale);

		//$PT = new POS_Terminal($_SESSION['pos-id']);
		//$PT->addMRUSale($this->Sale);

		Session::flash('info', 'Sale Confirmed, Transaction #' . $Sale['id']);

		return $RES->withRedirect('/pos/checkout/receipt?s=' . $Sale['id']);

	}

}
