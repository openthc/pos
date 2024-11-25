<?php
/**
 * POS Checkout Commit
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Checkout;

use Edoceo\Radix\Session;
use Edoceo\Radix\ULID;

use OpenTHC\Contact;

class Commit extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$_POST['cash_incoming'] = floatval($_POST['cash_incoming']);
		$_POST['cash_outgoing'] = floatval($_POST['cash_outgoing']);

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_POST['cart-id']);
		if (empty($Cart)) {
			throw new \Exception('Invalid Cart [PCC-027]');
		}
		$tz0 = new \DateTimezone($_SESSION['Company']['tz']);
		$dt0 = new \DateTime('now', $tz0);
		if (( ! empty($_POST['cart-date'])) && ( ! empty($_POST['cart-time'])) ) {
			$dtX = sprintf('%sT%s', $_POST['cart-date'], $_POST['cart-time']);
			$dt0 = new \DateTime($dtX, $tz0);
		}

		$dbc = $this->_container->DB;

		$Company = new \OpenTHC\Company($dbc, $_SESSION['Company']);
		$License = new \OpenTHC\License($dbc, $_SESSION['License']['id']);

		try {

			$dbc->query('BEGIN');

			$Sale = new \OpenTHC\POS\B2C\Sale($dbc);
			$Sale['id'] = ULID::create();
			$Sale['guid'] = $Sale['id'];
			$Sale['created_at'] = $dt0->format(\DateTime::RFC3339);
			$Sale['license_id'] = $License['id'];
			$Sale['contact_id'] = $_SESSION['Contact']['id'];
			$Sale['contact_id_client'] = $Cart->Contact->id;
			// $Sale['agent_contact_id'] = $_SESSION['Contact']['id'];//
			// $Sale['buyer_contact_id'] = //
			// $Sale['source_contact_id'] =
			// $Sale['target_contact_id'] =
			$Sale['meta'] = json_encode([
				'cash_incoming' => $_POST['cash_incoming'],
				'cash_outgoing' => $_POST['cash_outgoing'],
				'Cart' => $Cart,
				'_POST' => $_POST,
			]);
			// __exit_text($Sale);
			$Sale->save('B2C/Sale/Create');

			$b2c_base_price = 0;
			$b2c_full_price = 0;
			$b2c_item_count = 0;
			$b2c_item_adjust_total = 0;

			foreach ($Cart->item_list as $b2c_id => $b2c_item) {
				// @todo Need to Handle "Special" line items
				// Like, Loyalty or Tax or ??? -- Could those be "system" class Inventory to add to a ticket?
				// And Don't Decrement Them?
				$b2c_item->unit_count = floatval($b2c_item->unit_count);
				if ($b2c_item->unit_count <= 0) {
					continue;
				}

				$Inv = new \OpenTHC\POS\Inventory($dbc, $b2c_item->id);
				if (empty($Inv['id'])) {
					throw new \Exception('Inventory Lost on Sale [PCC-055]');
				}
				$Inv->decrement($b2c_item->unit_count);

				$P = new \OpenTHC\POS\Product($dbc, $Inv['product_id']);
				switch ($P['package_type']) {
				case 'pack':
				case 'each':
					$uom = 'ea';
					break;
				case 'bulk':
					$uom = new \OpenTHC\UOM($P['package_unit_uom']);
					$uom = $uom->getStub();
					break;
				}

				$SI = new \OpenTHC\POS\B2C\Sale\Item($dbc);
				$SI['id'] = ULID::create();
				$SI['b2c_sale_id'] = $Sale['id'];
				$SI['inventory_id'] = $Inv['id'];
				$SI['uom'] = $uom;
				$SI['unit_count'] = $b2c_item->unit_count;
				$SI['unit_price'] = floatval($Inv['sell']);
				if (isset($b2c_item->unit_price)) {
					$SI['unit_price'] = $b2c_item->unit_price;
				}
				$SI['base_price'] = ($SI['unit_price'] * $SI['unit_count']);
				// +Fees
				// -Discount
				// +/- Adjustment
				// +Tax
				$b2c_item_adjust_total = 0;
				foreach ($b2c_item->tax_list as $tax_ulid => $tax_line) {
					$b2c_item_adjust_total += $tax_line;
				}
				$SI['full_price'] = $SI['base_price'] + $b2c_item_adjust_total;
				$SI->save('B2C/Sale/Item/Create');

				// Add the Sale Item taxes Here
				foreach ($b2c_item->tax_list as $tax_ulid => $tax_line) {
					$dbc->insert('b2c_sale_item_adjust', [
						'id' => \Edoceo\Radix\ULID::create(),
						'b2c_sale_id' => $Sale['id'],
						'b2c_sale_item_id' => $SI['id'],
						'adjust_id' => $tax_ulid,
						'name' => 'Tax',
						'amount' => $tax_line,
					]);
				}

				$b2c_item_count += $SI['unit_count'];
				$b2c_base_price += $SI['base_price'];
				$b2c_full_price += $SI['full_price'];
				$b2c_item_adjust_total += $b2c_item_adjust_total;

			}

			$Sale['item_count'] = $b2c_item_count;
			$Sale['base_price'] = $b2c_base_price;
			$Sale['full_price'] = $b2c_full_price; // $Sale['base_price'] + $b2c_item_adjust_total;
			$Sale->save('B2C/Sale/Commit');

		} catch (\Exception $e) {
			_exit_html_fail(sprintf('<h1>Failed to Execute the Sale [PCC-123]</h1><pre>%s</pre>', __h($e->getMessage())), 500);
		}

		$Sale = $this->sendToCRE($Sale);
		$Sale->save('B2C/Sale/Update from CRE');

		$dbc->query('COMMIT');

		Session::flash('info', sprintf('Sale Confirmed, Transaction #%s', $Sale['guid']));

		// $key = sprintf('/%s/cart/%s', $_SESSION['License']['id'], $data['cart']['id']);
		// $rdb->del($key);

		$url = sprintf('/pos/checkout/receipt?s=%s', $Sale['id']);

		// if (is_ajax()) {
		if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			return $RES->withJSON([
				'data' => $url,
			]);
		}

		return $RES->withRedirect($url);

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
		case 'openthc':
			$Sale = $this->send_to_openthc($Sale);
			break;
		}

		return $Sale;
	}

	/**
	 * Execute Sale in BioTrack
	 */
	function send_to_biotrack($Sale)
	{
		switch ($_SESSION['cre']['id']) {
		case 'usa/nm':
			return $this->send_to_biotrack_v2022($Sale);
		default:
			return $this->send_to_biotrack_v2014($Sale);
		}
	}

	// Sale Dispense v1
	function send_to_biotrack_v2014($b2c_sale)
	{
		$dbc = $this->_container->DB;
		$rdb = $this->_container->Redis;

		$b2c_item_list = $b2c_sale->getItems();

		$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		$cre->setLicense($_SESSION['License']);
		// $res = $cre->card_lookup($_POST['mmj-mp'], $_POST['mmj-cg']);

		$b2c_term = '';
		$b2c_time = new \DateTime($b2c_sale['created_at'], new \DateTimezone('America/Denver'));

		$b2c_item_list = $b2c_sale->getItems();

		$inv_list = [];
		foreach ($b2c_item_list as $b2c_item) {
			$Inv = new Inventory($m[1]);
			if ($Inv->isRegulated()) {
				$inv_list[] = array(
					'barcodeid' => $Inv['guid'],
					'quantity' => intval($b2c_item['unit_count']),
					'price' => sprintf('%0.2f', $b2c_item['unit_price']),
				);
			}
		}

		if (count($inv_list)) {
			$res = $cre->sale_dispense($inv_list, $b2c_time->format('U'));
			switch ($res['success']) {
			case 0:
				// Tri
				Session::flash('fail', $cre->formatError($res));
				Radix::redirect('/pos/sale?id=' . $b2c_sale['id']);
				break;
			case 1:
				$b2c_sale['guid'] = sprintf('tid:%s', $res['transactionid']);
				Session::flash('info', "Sale {$b2c_sale['id']} Assigned Transaction {$res['transactionid']}");
				break;
			}
		} else {
			// UnRegulated Sale?
			// ??
		}

		return $b2c_sale;

	}

	/**
	 *
	 */
	function send_to_biotrack_v2022($b2c_sale)
	{
		// New Stuff Here
		$dbc = $this->_container->DB;
		$rdb = $this->_container->Redis;

		$tz0 = new \DateTimezone($_SESSION['Company']['tz']);
		$b2c_time = new \DateTime($b2c_sale['created_at'], $tz0);
		$b2c_time->setTimezone($tz0);

		$b2c_item_list = $b2c_sale->getItems();

		// Sale Dispense v3
		// https://documenter.getpostman.com/view/15944043/UVktqDR2#bee52c63-f4bf-46ce-a6d2-34099afdb09b
		/*
		Client error: `
		POST https://v3.api.nm.trace.biotrackthc.net/v1/dispense` resulted in a `400 Bad Request` response:
		 {"Error":"Error reading JSON body:
		 parsing time \"\"2024-09-28T16:20:00-0600\"\" as \"\"2006-01-02T15:04:05Z07:00\"\":
		 c (truncated...
		*/
		// "Error": "Error reading JSON body: parsing time \"\"2024-09-28T16:20:00-0600\"\" as \"\"2006-01-02T15:04:05Z07:00\"\": cannot parse \"-0600\"\" as \"Z07:00\""
		// It's using GOLANG as the Parser, so properly put all things in floatval
		$req = [];
		$req['LocationLicense'] = $_SESSION['License']['code'];
		$req['Type'] = 'RECREATIONAL';
		// https://www.php.net/manual/en/class.datetimeinterface.php#datetimeinterface.constants.iso8601
		// PHP ISO8601 is NOT CORRECT, so use ATOM
		$req['Datetime'] = $b2c_time->format(\DateTimeInterface::ATOM);
		$req['RequestID'] = $b2c_sale['id'];
		$req['ExternalID'] = $b2c_sale['id'];
		// $req['PatientCardKey'] = '';
		// $req['TerminalID'] => $b2c_term;

		$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
		if ( ! $Contact->loadBy('id', $b2c_sale['contact_id_client'])) {
			throw new \Exception('Invalid Contact', 500);
		}
		$m = $Contact->getMeta();
		if ( ! empty($m['CardID'])) {
			$req['Type'] = 'MEDICAL';
			$req['PatientCardKey'] = $m['CardKey'];
		}

		$req['Items'] = [];
		foreach ($b2c_item_list as $b2c_item) {
			// $I = new Inventory($b2c_item['inventory_id']);
			$Inv = new \OpenTHC\POS\Inventory($dbc, $b2c_item['inventory_id']);
			$tmp_item = [
				'Barcode' => $Inv['guid'],
				'Quantity' => floatval($b2c_item['unit_count']),
				'Price' => floatval(sprintf('%0.2f', $b2c_item['unit_price'])),
				'Tax' => [
					'Excise' => 0,
					'Other' => 0,
				]
			];

			// Add Adjustments/Taxes/etc
			$res_adjust = $dbc->fetchAll('SELECT * FROM b2c_sale_item_adjust WHERE b2c_sale_item_id = :bi0', [
				':bi0' => $b2c_item['id'],
			]);

			foreach ($res_adjust as $adj) {
				switch ($adj['adjust_id']) {
				case '010PENTHC00BIPA0SST03Q484J':
					$tmp_item['Tax']['Other'] = floatval($adj['amount']);
				}
			}

			$req['Items'][] = $tmp_item;

		}

		// Authenticate and then Checkout

		// Needs a good CRE-Adapter or BONG to work
		$ghc = new \GuzzleHttp\Client([
			// 'base_uri' => 'https://v3.api.nm.trace.biotrackthc.net/',
			'base_uri' => 'https://pipe.openthc.com/biotrack/v3.api.nm.trace.biotrackthc.net/',
			// 'base_uri' => 'https://bunk.openthc.dev/biotrack/v2022/',
			'http_errors' => false,
			// 'cookie'
			'headers' => [
				'openthc-contact-id' => $_SESSION['Contact']['id'],
				'openthc-company-id' => $_SESSION['Company']['id'],
				'openthc-license-id' => $_SESSION['License']['id'],
			]
		]);

		$key = sprintf('/license/%s/cre/biotrack2023/sid', $_SESSION['License']['id']);
		$sid = $rdb->get($key);
		if (empty($sid)) {

			// This provides a JWT in the Session
			// It's valid for like 7 days
			$res = $ghc->post('v1/login', [ 'json' => [
				'UBI' => $_SESSION['Company']['cre_meta']['company'],
				'Username' => $_SESSION['Company']['cre_meta']['username'],
				'Password' => $_SESSION['Company']['cre_meta']['password'],
			]]);

			$res = $res->getBody()->getContents();
			$res = json_decode($res);
			$sid = $res->Session;

			$rdb->set($key, $sid, [ 'ex' => 3600 ]);
		}
		// __exit_text([
		// 	'sid' => $sid,
		// 	'req' => $req
		// ]);

		$res = $ghc->post('v1/dispense', [
			'json' => $req,
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $sid)
			]
		]);
		$res = $res->getBody()->getContents();
		$res = json_decode($res);
		if (empty($res->TransactionID)) {
			throw new \Exception('Failed to Execute Transaction in CRE [PCC-354]');
		}

		$b2c_sale['guid'] = sprintf('tid:%s', $res->TransactionID);

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

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_POST['cart-id']);

		$obj = [];
		$obj['SalesDateTime'] = date(\DateTime::RFC3339);
		$obj['SalesCustomerType'] = 'Patient'; // 'Consumer', 'Caregiver', 'ExternalPatient';

		// 'Consumer', 'Caregiver'; 'ExternalPatient', 'Patient'
		switch ($Cart->Contact->id) {
			case '018NY6XC00C0NTACT000WALK1N':
				$obj['SalesCustomerType'] = 'Consumer';
				break;
			default:
				$obj['SalesCustomerType'] = 'Patient';
				$obj['PatientLicenseNumber'] = $Cart->Contact->guid;
				break;
		}
		switch ($Cart->Contact->type) {
			case '018NY6XC00C0NTACTTYPE000AC':
				$obj['SalesCustomerType'] = 'Consumer';
				break;
			case '018NY6XC00C0NTACTTYPE000PA': // Well Known ULID
				$obj['SalesCustomerType'] = 'Patient';
				$obj['PatientLicenseNumber'] = $Cart->Contact->guid;
				break;
		}

		// @todo Fix assumptions about Customer, add Patient/Caregiver UX
		// $obj['PatientLicenseNumber'] = '12-345-678-DD'; //  $Sale['contact_list']['']; '000001';
		// $obj['CaregiverLicenseNumber'] = 'CLN-DEF456'; // $Sale['contact_list']['']; '000001';
		// $obj['IdentificationMethod'] = 'ID';
		// $obj['PatientRegistrationLocationId'] = '';

		$obj['Transactions'] = [];

		$b2c_item_list = $Sale->getItems();
		foreach ($b2c_item_list as $b2c_item) {
			$inv = new \OpenTHC\POS\Inventory($dbc, $b2c_item['inventory_id']);
			$uom = new \OpenTHC\UOM($b2c_item['uom']);
			$uom = $uom->getName();
			$obj['Transactions'][] = [
				// 'CityTax' => null,
				// 'CountyTax' => null,
				// 'DiscountAmount' => null,
				// 'ExciseTax' => null,
				'InvoiceNumber' => $b2c_item['id'],
				// 'MunicipalTax' => null,
				'PackageLabel' => $inv['guid'],
				// 'Price' => $b2c_item['unit_price'],
				'Quantity' => $b2c_item['unit_count'],
				// 'SalesTax' => null,
				// 'SubTotal' => $b2c_item['unit_price'],
				'TotalAmount' => ($b2c_item['unit_price'] * $b2c_item['unit_count']),
				'UnitOfMeasure' => $uom,
				// 'UnitThcContent' => null,
				// 'UnitThcContentUnitOfMeasure' => null,
				// 'UnitThcPercent' => null,
				// 'UnitWeight' => null,
				// 'UnitWeightUnitOfMeasure' => null,
			];
		}

		$api = $cre->b2c();
		$res = $api->create($obj);

		$m = $Sale->getMeta();
		$m['@cre']['result'] = $res;
		$Sale['meta'] = json_encode($m);
		$Sale['stat'] = $res['code'];

		switch ($res['code']) {
			case 200:
				// Great
				break;
			default:
				Session::flash('warn', $cre->formatError($res));
				break;
		}
		// if (200 == $res['code']) {
			// This is not finding the transaction
			// $cre->setTimeAlpha(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] - 60));
			// $cre->setTimeOmega(date(\DateTime::ISO8601, $_SERVER['REQUEST_TIME'] + 60));
			// $res = $api->search('active');
			// foreach ($res['data'] as $chk_b2c) {
			// 	$objB = $api->single($chk_b2c['Id']);
			// }
		// }

		return $Sale;

	}

	/**
	 *
	 */
	function send_to_openthc($Sale)
	{
		throw new \Exception('Not Implemented');

		$cfg = $_SESSION['cre'];
		$cfg['contact'] = $_SESSION['Contact']['id'];
		$cfg['company'] = $_SESSION['Company']['id'];
		$cfg['license'] = $_SESSION['License']['id'];
		$cre = \OpenTHC\CRE::factory($cfg);
		$cre->setLicense($_SESSION['License']);
		$res = $cre->auth([]);

		// $res = $cre->b2c()->create($Sale);

		$b2c_item_list = $Sale->getItems();
		foreach ($b2c_item_list as $b2c_item) {
			// $cre->b2c($Sale['id'])->addItem($Sale['id'], $b2c_item);
		}

		// $cre->b2c($Sale['id'])->commit();

		return $Sale;

	}

}
