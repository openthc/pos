<?php
/**
 * POS Checkout Commit
*/

namespace App\Controller\POS\Checkout;

use Edoceo\Radix\Session;
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
			$Sale['guid'] = $Sale['id'];
			$Sale['meta'] = json_encode($_POST);
			$Sale->save();

			$key_list = array_keys($_POST);
			foreach ($key_list as $key) {
				// @todo Need to Handle "Special" line items
				// Like, Loyalty or Tax or ??? -- Could those be "system" class Inventory to add to a ticket?
				// And Don't Decrement Them?
				if (preg_match('/^qty\-(\w+)/', $key, $m)) {

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
			// $License->opt('tax-retail-rate') ??
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
				$SI['qom'] = 0;
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

		} catch (\Exception $e) {
			_exit_fail('<h1>Failed to Execute the Sale [PCC-123]</h1>', 500);
		}

		$this->sendToCRE($Sale);

		$dbc->query('COMMIT');

		Session::flash('info', 'Sale Confirmed, Transaction #' . $Sale['id']);

		return $RES->withRedirect('/pos/checkout/receipt?s=' . $Sale['id']);

	}

	/**
	 * Send the Sale to the CRE
	 */
	function sendToCRE($Sale)
	{
		$dbc = $this->_container->DB;

		switch ($_SESSION['cre']['engine']) {
			case 'biotrack':
				$this->send_to_biotrack($Sale);
				break;
			case 'leafdata':
				$this->send_to_leafdata($Sale);
				break;
			case 'metrc':
				$this->send_to_metrc($Sale);
				break;
		}

	}

	/**
	 * Execute Sale in BioTrack
	 */
	function send_to_biotrack($b2c_sale)
	{
		$rbe = App::rbe();
		// $res = $rbe->card_lookup($_POST['mmj-mp'], $_POST['mmj-cg']);

		$S['json'] = json_decode($S['json'], true);

		$inv_list = array();
		foreach ($S['json'] as $k => $v) {

			if (preg_match('/^item\-(\d+)$/', $k, $m)) {

				$I = new Inventory($m[1]);
				$s = $S['json'][sprintf('size-%d', $I['id'])];

				if ($I->isRegulated()) {
					$inv_list[] = array(
						'barcodeid' => $I['guid'],
						'quantity' => intval($s),
						'price' => sprintf('%0.2f', $I['sell']),
					);
				}
			}
		}

		throw new Exception('What to Do HEre');

		if (count($inv_list)) {
			$res = $rbe->sale_dispense($inv_list, strtotime($S['dts']));
			switch ($res['success']) {
			case 0:
				// Tri
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

		return $b2c_sale;

	}

	/**
	 * Execute Sale in LeafData
	 */
	function send_to_leafdata($b2c_sale)
	{
		$dbc = $this->_container->DB;

		$obj = [];
		$obj['external_id'] = $b2c_sale['id'];
		$obj['type'] = 'retail_recreational';
		$obj['status'] = 'sale';
		$obj['sold_at'] = date(\OpenTHC\CRE\LeafData::FORMAT_DATE_TIME);
		// $obj['global_sold_by_user_id'] = '';
		// $obj['patient_medical_id']
		// $obj['caregiver_id']
		$obj['price_total'] = 0;
		$obj['sale_items'] = [];

		// Get Items
		$b2c_item_list = $b2c_sale->getItems();
		foreach ($b2c_item_list as $b2c_item) {

			$lot = new \App\Lot($dbc, $b2c_item['inventory_id']);
			$Product = new \App\Product($dbc, $lot['product_id']);

			$obj['sale_items'][] = [
				'global_inventory_id' => $lot['guid'],
				// 'global_batch_id' =>
				'external_id' => $b2c_item['id'],
				'type' => $obj['type'],
				'sold_at' => $obj['sold_at'],
				'qty' => $b2c_item['qty'],
				'uom' => 'ea',
				'unit_price' => $b2c_item['unit_price'],
				'name' => $Product['name'],
			];

			$obj['price_total'] = $obj['price_total'] + ($b2c_item['unit_price'] * $b2c_item['qty']);
		}

		$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		$cre->setLicense($_SESSION['License']);

		$res = $cre->b2c()->create($obj);
		if (200 != $res['code']) {
			Session::flash('fail', $cre->formatError($res));
		}

		$b2c_sale['guid'] = $res['data'][0]['global_id'];
		$b2c_sale['meta'] = json_encode($res['data'][0]);
		// $b2c_sale->setMeta($res['data'][0], 'B2C/Sale/Created');
		$b2c_sale->save();

		return $b2c_sale;

	}

	/**
	 * Execute Sale in Metrc
	 */
	function send_to_metrc($Sale)
	{
		$dbc = $this->_container->DB;

		$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		$cre->setLicense($_SESSION['License']);

		// $req = $cre->_curl_init($cre->_make_url('/unitsofmeasure/v1/active'));
		// $res = $cre->_curl_exec($req);

		// $req = $cre->_curl_init($cre->_make_url('/sales/v1/customertypes'));
		// $res = $cre->_curl_exec($req);
		// exit;

		$obj = [];
		$obj['SalesDateTime'] = date(\DateTime::ISO8601);
		$obj['SalesCustomerType'] = 'Consumer';
		// Consumer" [1] => string(7) "Patient" [2] => string(9) "Caregiver" [3] => string(15) "ExternalPatient"
		$obj['PatientLicenseNumber'] = 'ABC-123';
		// "CaregiverLicenseNumber": null,
		// "IdentificationMethod": null,
		$obj['Transactions'] = [];

		$b2c_item_list = $Sale->getItems();
		foreach ($b2c_item_list as $b2c_item) {
			$lot = new \App\Lot($dbc, $b2c_item['inventory_id']);
			$obj['Transactions'][] = [
				'PackageLabel' => $lot['guid'],
				'Quantity' => $b2c_item['qty'],
				'UnitOfMeasure' => 'Grams',
				'TotalAmount' => ($b2c_item['unit_price'] * $b2c_item['qty']),
			];
		}

		$api = $cre->b2c();

		$res = $api->create($obj);
		if (200 == $res['code']) {

			$cre->setTimeAlpha(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] - 60));
			$cre->setTimeOmega(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] + 60));
			$chk = $api->search('active');

			foreach ($chk['data'] as $chk_b2c) {
				$obj8 = $api->single($chk_b2c['Id']);
			}

		}

		return $Sale;

	}

}
