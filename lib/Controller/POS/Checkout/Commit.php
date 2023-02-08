<?php
/**
 * POS Checkout Commit
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Checkout;

use Edoceo\Radix\Session;
use Edoceo\Radix\ULID;

class Commit extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$_POST['cash_incoming'] = floatval($_POST['cash_incoming']);
		$_POST['cash_outgoing'] = floatval($_POST['cash_outgoing']);

		$dbc = $this->_container->DB;

		$Company = new \OpenTHC\Company($dbc, $_SESSION['Company']);
		$License = new \OpenTHC\License($dbc, $_SESSION['License']['id']);

		try {

			$dbc->query('BEGIN');

			$b2c_item_count = 0;
			$sum_item_price = 0;

			$Sale = new \OpenTHC\POS\B2C\Sale($dbc);
			$Sale['id'] = ULID::create();
			$Sale['license_id'] = $License['id'];
			$Sale['contact_id'] = $_SESSION['Contact']['id'];
			$Sale['guid'] = $Sale['id'];
			$Sale['meta'] = json_encode([
				'_SESSION/Checkout' => $_SESSION['Checkout'],
				'_POST' => $_POST,
			]);
			$Sale->save('B2C/Sale/Create');

			$key_list = array_keys($_POST);
			foreach ($key_list as $key) {
				// @todo Need to Handle "Special" line items
				// Like, Loyalty or Tax or ??? -- Could those be "system" class Inventory to add to a ticket?
				// And Don't Decrement Them?
				if (preg_match('/^qty\-(\w+)/', $key, $m)) {

					$qty = floatval($_POST[$key]);
					if ($qty <= 0) {
						continue;
					}

					$IL = new \OpenTHC\POS\Lot($dbc, $m[1]);
					if (empty($IL['id'])) {
						throw new \Exception('Inventory Lost on Sale [PCC-055]');
					}

					$P = new \OpenTHC\POS\Product($dbc, $IL['product_id']);
					switch ($P['package_type']) {
						case 'pack':
						case 'each':
							$b2c_item_count += $qty;
							$uom = 'ea';
							break;
						case 'bulk':
							$b2c_item_count++;
							$uom = new \OpenTHC\UOM($P['package_unit_uom']);
							$uom = $uom->getStub();
							break;
					}

					$SI = new \OpenTHC\POS\B2C\Sale\Item($dbc);
					$SI['id'] = ULID::create();
					$SI['b2c_sale_id'] = $Sale['id'];
					$SI['inventory_id'] = $IL['id'];
					$SI['unit_count'] = $qty;
					$SI['unit_price']= floatval($IL['sell']);
					$SI['uom'] = $uom;
					$SI->save('B2C/Sale/Item/Create');

					$IL->decrement($qty);

					$sum_item_price += ($SI['unit_price'] * $SI['unit_count']);

				}
			}

			// Excise Taxes
			// $opt_help->get('/%s/)
			// $Company->getOption(sprintf('%s/', $_SESSION['License']['id']));
			$arg = [
				':k' => sprintf('/%s/tax-excise-rate', $_SESSION['License']['id']),
			];
			$tax_excise_rate = $dbc->fetchOne('SELECT val FROM base_option WHERE key = :k', $arg);
			$tax_excise_rate = floatval($tax_excise_rate);
			if ($tax_excise_rate > 1) {
				$tax_excise_rate = $tax_excise_rate / 100;
			}
			if ($tax_excise_rate > 0) {
				$SI = new \OpenTHC\POS\B2C\Sale\Item($dbc);
				$SI['id'] = ULID::create();
				$SI['b2c_sale_id'] = $Sale['id'];
				$SI['inventory_id'] = -1;
				$SI['guid'] = '-';
				$SI['unit_count'] = 1;
				$SI['unit_price'] = ($sum_item_price * $tax_excise_rate);
				$SI->setFlag(\OpenTHC\POS\B2C\Sale\Item::FLAG_TAX_EXCISE);
				$SI->save();
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
				$SI = new \OpenTHC\POS\B2C\Sale\Item($dbc);
				$SI['id'] = ULID::create();
				$SI['b2c_sale_id'] = $Sale['id'];
				$SI['inventory_id'] = -1;
				$SI['guid'] = '-';
				$SI['unit_count'] = 1;
				$SI['unit_price'] = ($sum_item_price * $tax_excise_rate);
				$SI->setFlag(\OpenTHC\POS\B2C\Sale\Item::FLAG_TAX_RETAIL);
				$SI->save();
			}

			$Sale['item_count'] = $b2c_item_count;
			$Sale['full_price'] = $sum_item_price + $tax0 + $tax1;
			$Sale->save('B2C/Sale/Commit');

		} catch (\Exception $e) {
			_exit_html_fail(sprintf('<h1>Failed to Execute the Sale [PCC-123]</h1><pre>%s</pre>', __h($e->getMessage())), 500);
		}

		$Sale = $this->sendToCRE($Sale);
		$Sale->save('B2C/Sale/Update from CRE');

		$dbc->query('COMMIT');

		Session::flash('info', 'Sale Confirmed, Transaction #' . $Sale['id']);

		return $RES->withRedirect('/pos/checkout/receipt?s=' . $Sale['id']);

	}

	/**
	 * Send the Sale to the CRE
	 */
	function sendToCRE($Sale)
	{
		switch ($_SESSION['cre']['engine']) {
			case 'biotrack':
				$Sale = $this->send_to_biotrack($Sale);
				break;
			case 'metrc':
				$Sale = $this->send_to_metrc($Sale);
				break;
		}

		return $Sale;
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
	 * Execute Sale in Metrc
	 */
	function send_to_metrc($Sale)
	{
		$dbc = $this->_container->DB;

		$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		$cre->setLicense($_SESSION['License']);

		$obj = [];
		$obj['SalesCustomerType'] = 'Patient'; // 'Consumer',  'Caregiver'; 'ExternalPatient';
		$obj['SalesDateTime'] = date(\DateTime::ISO8601);

		// @todo Fix assumptions about Customer, add Patient/Caregiver UX
		// $obj['PatientLicenseNumber'] = '12-345-678-DD'; //  $Sale['contact_list']['']; '000001';
		// $obj['CaregiverLicenseNumber'] = 'CLN-DEF456'; // $Sale['contact_list']['']; '000001';
		// $obj['IdentificationMethod'] = 'ID';
		// $obj['PatientRegistrationLocationId'] = '';

		$obj['Transactions'] = [];

		$b2c_item_list = $Sale->getItems();
		foreach ($b2c_item_list as $b2c_item) {
			$lot = new \OpenTHC\POS\Lot($dbc, $b2c_item['inventory_id']);
			$uom = new \OpenTHC\UOM($b2c_item['uom']);
			$uom = $uom->getName();
			$obj['Transactions'][] = [
				'CityTax' => null,
				'CountyTax' => null,
				'DiscountAmount' => null,
				'ExciseTax' => null,
				'InvoiceNumber' => null,
				'MunicipalTax' => null,
				'PackageLabel' => $lot['guid'],
				'Price' => $b2c_item['unit_price'],
				'Quantity' => $b2c_item['unit_count'],
				'SalesTax' => null,
				'SubTotal' => null,
				'TotalAmount' => ($b2c_item['unit_price'] * $b2c_item['unit_count']),
				'UnitOfMeasure' => $uom,
				'UnitThcContent' => null,
				'UnitThcContentUnitOfMeasure' => null,
				'UnitThcPercent' => '1',
				'UnitWeight' => null,
				'UnitWeightUnitOfMeasure' => null,
			];
		}

		$api = $cre->b2c();
		$res = $api->create($obj);

		$m = $Sale->getMeta();
		$m['@cre']['result'] = $res;
		$Sale['meta'] = json_encode($m);
		$Sale['stat'] = $res['code'];

		if (200 == $res['code']) {
			// This is not finding the transaction
			// $cre->setTimeAlpha(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] - 60));
			// $cre->setTimeOmega(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] + 60));
			// $res = $api->search('active');
			// foreach ($res['data'] as $chk_b2c) {
			// 	$objB = $api->single($chk_b2c['Id']);
			// }
		}

		return $Sale;

	}

}
