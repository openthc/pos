<?php
/**
 * POS Checkout Commit
*/

namespace App\Controller\POS\Checkout;

use Edoceo\Radix\Session;
use Edoceo\Radix\DB\SQL;
use Edoceo\Radix\ULID;

class Commit extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$License = new \OpenTHC\License($dbc, $_SESSION['License']['id']);

		try {

			$dbc->query('BEGIN');

			$sum_item_price = 0;
			$idx_sort = 1;

			$Sale = new \App\B2C\Sale($dbc);
			$Sale['id'] = ULID::create();
			$Sale['license_id'] = $License['id'];
			$Sale['contact_id'] = $_SESSION['Contact']['id'];
			$Sale['guid'] = '-';
			$Sale['meta'] = json_encode($_POST);
			$Sale->save();

			$key_list = array_keys($_POST);
			foreach ($key_list as $key) {
				// @todo Need to Handle "Special" line items
				// Like, Loyalty or Tax or ??? -- Could those be "system" class Inventory to add to a ticket?
				// And Don't Decrement Them?
				if (preg_match('/^qty\-(\d+)/', $key, $m)) {

					$qty = floatval($_POST[$key]);
					$IL = new \App\Lot($dbc, $m[1]);

					if ($IL['id']) {

						$SI = new \App\B2C\Sale\Item($dbc);
						$SI['id'] = ULID::create();
						$SI['b2c_sale_id'] = $Sale['id'];
						$SI['inventory_id'] = $IL['id'];
						$SI['qty'] = $qty;
						$SI['qom'] = 0; // $IL['package_size'];
						$SI['unit_price']= $IL['sell'];
						$SI['uom'] = 'ea';
						// $SI['sort'] = $idx_sort;
						$SI->save();

						$IL->decrement($qty);

						$sum_item_price += ($SI['unit_price'] * $SI['qty']);
						$idx_sort++;

					}
				}
			}

			// Excise Taxes
			$arg = [
				':k' => sprintf('/%s/tax-excise-rate', $_SESSION['License']['id']),
			];
			$tax_excise_rate = $dbc->fetchOne('SELECT val FROM base_option WHERE key = :k', $arg);
			$tax_excise_rate = floatval($tax_excise_rate);
			if ($tax_excise_rate > 1) {
				$tax_excise_rate = $tax_excise_rate / 100;
			}
			if ($tax_excise_rate > 0) {
				$SI = new \App\B2C\Sale\Item($dbc);
				$SI['id'] = ULID::create();
				$SI['b2c_sale_id'] = $Sale['id'];
				$SI['inventory_id'] = -1;
				$SI['guid'] = '-';
				$SI['qty'] = 1;
				$SI['qom'] = 0; // $IL['package_size'];
				$SI['unit_price'] = ($sum_item_price * $tax_excise_rate);
				$SI['uom'] = 'ea';
				// $SI['sort'] = $idx_sort;
				$SI->setFlag(\App\B2C\Sale\Item::FLAG_TAX_EXCISE);
				$SI->save();
				// $idx_sort++;
			}

			// Retail/Sales Taxes
			$arg = [
				':k' => sprintf('/%s/tax-retail-rate', $_SESSION['License']['id']),
			];
			$tax_retail_rate = $dbc->fetchOne('SELECT val FROM base_option WHERE key = :k', $arg);
			$tax_retail_rate = floatval($tax_retail_rate);
			if ($tax_retail_rate > 1) {
				$tax_retail_rate = $tax_retail_rate / 100;
			}
			if ($tax_retail_rate > 0) {
				$SI = new \App\B2C\Sale\Item($dbc);
				$SI['id'] = ULID::create();
				$SI['b2c_sale_id'] = $Sale['id'];
				$SI['inventory_id'] = -1;
				$SI['guid'] = '-';
				$SI['qty'] = 1;
				$SI['qom'] = 0; // $IL['package_size'];
				$SI['unit_price'] = ($sum_item_price * $tax_excise_rate);
				$SI['uom'] = 'ea';
				$SI->setFlag(\App\B2C\Sale\Item::FLAG_TAX_RETAIL);
				// $SI['sort'] = $idx_sort;
				$SI->save();
				// $idx_sort++;
			}

			$Sale['list_price'] = $sum_item_price; // $_POST['sub'];
			$Sale['full_price'] = $Sale['list_price'] + $tax0 + $tax1;
			$Sale->save();

			$dbc->query('COMMIT');

		} catch (Exception $e) {
			_exit_text('Failed to Execute the Sale', 500);
		}

		$this->sendToCRE($Sale);

		//$PT = new POS_Terminal($_SESSION['pos-id']);
		//$PT->addMRUSale($this->Sale);

		Session::flash('info', 'Sale Confirmed, Transaction #' . $Sale['id']);

		return $RES->withRedirect('/pos/checkout/receipt?s=' . $Sale['id']);

	}

	function sendToCRE($Sale)
	{
		$dbc = $this->_container->DB;

		$req = [
			'created_at' => $Sale['created_at'],
			'type' => 'Consumer',
			'item_list' => [],
		];
		foreach ($Sale->getItems() as $SI) {

			$L = new \App\Lot($dbc, $SI['inventory_id']);

			$req['item_list'][] = [
				'guid' => $L['guid'],
				'qty' => $SI['qty'],
				'uom' => $SI['uom'],
				'full_price' => $SI['unit_price'] * $SI['qty'],
			];
		}

		// $cre = CRE::factory($_SESSION['cre']);
		// $res = $cre->b2c->sale()->create($req);

		switch ($_SESSION['cre']['engine']) {
			case 'metrc':
				throw new Exception('Cannot Sell in Oregon');
				break;
			case 'biotrack':
				_exec_sale_in_rbe_wa($S);
				break;
			case 'leafdata':
				_exec_sale_in_rbe_wa_leaf($S);
				break;
		}

	}
}


function _exec_sale_in_rbe_wa($S)
{
	$rbe = App::rbe();

	// Radix::dump($_SESSION);
	// Radix::dump($task);
	$S['json'] = json_decode($S['json'], true);
	// Radix::dump($S);

	$inv_list = array();
	foreach ($S['json'] as $k => $v) {

		if (preg_match('/^item\-(\d+)$/', $k, $m)) {

			$I = new Inventory($m[1]);
			$s = $S['json'][sprintf('size-%d', $I['id'])];
			// print_r($I);

			if ($I->isRegulated()) {
				$inv_list[] = array(
					'barcodeid' => $I['guid'],
					'quantity' => intval($s),
					'price' => sprintf('%0.2f', $I['sell']),
				);
			}
		}
	}
	// print_r($inv_list);

	throw new Exception('What to Do HEre');

	if (count($inv_list)) {
		$res = $rbe->sale_dispense($inv_list, strtotime($S['dts']));
		switch ($res['success']) {
		case 0:
			// Tri
			//print_r($res);
			Session::flash('fail', $rbe->formatError($res));
			Radix::redirect('/pos/sale?id=' . $S['id']);
			break;
		case 1:
			$S['tid'] = $res['transactionid'];
			Session::flash('info', "Sale {$S['id']} Assigned Transaction {$S['tid']}");
			//syslog(LOG_NOTICE, "Sale {$S['id']} Assigned Transaction {$S['tid']}");
			$S->save();
			//Task::done($task);
			break;
		}
	} else {
		// UnRegulated Sale?
		// ??
	}

}

function _exec_sale_in_rbe_wa_leaf($S)
{
	$rbe = App::rbe();

	return false;

}
