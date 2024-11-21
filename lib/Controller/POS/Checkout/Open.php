<?php
/**
 * Open the Checkout Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Checkout;

use Edoceo\Radix\Session;
use Edoceo\Radix\ULID;

use OpenTHC\Contact;

class Open extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'POS :: Checkout :: Open')
		);

		return $RES->write( $this->render('pos/checkout/done.php', $data) );
	}

	/**
	 *
	 */
	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
			case 'client-contact-commit':
				return $this->contact_commit($RES);
			case 'client-contact-search':
				return $this->contact_search($RES);
			case 'client-contact-skip':
				$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);
				$Cart->Contact = [
					'id' => '018NY6XC00C0NTACT000WALK1N',
					'stat' => 200,
					'name' => 'Walk In',
				];
				$Cart->save();
				return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));
			case 'client-contact-update':
				return $this->contact_open($RES);
			case 'client-contact-update-force':
				$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);
				$Contact1 = new Contact($this->_container->DB);
				if ( ! $Contact1->loadBy('guid', $_POST['client-contact-govt-id'])) {
					$Contact1['id'] = ULID::create();
					$Contact1['stat'] = 100;
					$Contact1['guid'] = $_POST['client-contact-govt-id'];
					$Contact1['hash'] = '-';
					$Contact1['type'] = 'b2c-client';
					$Contact1->save('Contact/Create in POS by User');
					Session::flash('info', 'New Contact! Please add necessary details');
				};
				$Cart->Contact = $Contact1->toArray();
				$Cart->save();
				return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));
				break;
			default:
				Session::flash('fail', 'Invalid Requset [PCO-045]');
				return $RES->withRedirect('/pos');
		}
	}

	/**
	 * Commit the Contact
	 */
	function contact_commit($RES)
	{
		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);
		$Contact = new Contact($this->_container->DB, $Cart->Contact->id);

		$Contact['stat'] = Contact::STAT_LIVE;
		$Contact['fullname'] = $_POST['client-contact-name'];
		$Contact['code'] = $_POST['client-contact-code'];
		$m = $Contact->getMeta();
		$m['client-type'] = $_POST['client-contact-type'];
		$m['dob'] = $_POST['client-contact-dob'];
		$Contact['meta'] = json_encode($m);

		$Contact->save('Contact/Update by User');

		$Cart->Contact = $Contact->toArray();
		$Cart->save();

		return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));

	}

	/**
	 *
	 */
	function contact_open($RES)
	{
		switch ($_SESSION['cre']['id']) {
		case 'usa/ok':
			// Has to Lookup on External Site
			return $this->_contact_search_usa_ok($RES);
		case 'usa/mt':
			// Has to Lookup on External Site
			return $this->_contact_search_usa_mt($RES);
		case 'usa/nm':
			switch ($_POST['pos-cart-type']) {
			case 'MED':
				return $this->_contact_search_usa_nm_med($RES);
			}
		}

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);

		$dbc = $this->_container->DB;

		// $code0 = $_POST['client-contact-pid'];

		$gov_id = null;
		$gov_id_type = null;
		$guid0 = $_POST['client-contact-govt-id'];
		if (preg_match('/^([\w\- ]{4,32})$/', $guid0)) {
			// Manual Entry
			$guid0 = trim($guid0);
			$gov_id_type = 'CONTACT_ID';
		} elseif (preg_match('/.*ANSI.*/', $guid0)) {
			// Scanned
			$gov_id_type = 'STATE_ID';
		}

		$guid1 = $_POST['client-contact-govt-id'];
		if (preg_match('/(\w+)\s*\/\s*(\w+)/', $guid0, $m)) {
			$guid1 = $m[2];
		}

		$res_contact = $this->contact_search($guid0, $guid1);

		switch ($_SESSION['cre']['id']) {
		case 'usa/mt':
		case 'usa/or':
			$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
			$cre->setLicense($_SESSION['License']);
			$res = $cre->contact()->single($guid1);
			switch ($res['code']) {
				case 200:
					$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
					if ( ! $Contact->loadBy('guid', $res['data']['PatientId'])) {
						$Contact['id'] = ULID::create();
						$Contact['stat'] = 100;
						$Contact['guid'] = $res['data']['PatientId'];
						$Contact['hash'] = '-';
						$Contact['type'] = 'b2c-client';
						$Contact['fullname'] = ''; // $_POST['client-contact-name'];
						$Contact['meta'] = json_encode([
							'@cre' => $res['data']
						]);
						$Contact->save('Contact/Create in POS by User');
					};

					if ( ! empty($Contact['id'])) {
						$Cart->Contact = $Contact->toArray();
						$Cart->save();
						return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));
					}

					break;
				case 404:
					$Cart->Contact = [
						'id' => '',
						'guid' => $guid1,
						'stat' => 100,
						'type' => 'b2c-client',
					];
					$Cart->save();
					return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));
				default:
					// Session::flash('fail', $cre->formatError($res));
					// return $RES->withRedirect('/pos');
			}

			break;

		case 'deu':
		case 'usa/nm':
		case 'usa/vt':
		case 'usa/wa':

			$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
			if ( ! $Contact->loadBy('guid', $guid0)) {
				$Contact['id'] = ULID::create();
				$Contact['stat'] = 100;
				$Contact['guid'] = $guid1;
				$Contact['hash'] = '-';
				$Contact['type'] = 'b2c-client';
				$Contact['fullname'] = '';
				$Contact->save('Contact/Create in POS by User');
			}

			break;

		default:
			Session::flash('warn', sprintf('Unsupported CRE: %s', $_SESSION['cre']['id']));
		}

		if (empty($Contact['id'])) {
			Session::flash('fail', 'Cannot find Client Contact');
			return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));
		}

		$Cart->type = 'REC';
		$Cart->Contact = $Contact->toArray();
		$Cart->save();

		return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));

	}

	/**
	 * Searches the existing contact database
	 */
	function contact_search($guid0, $guid1)
	{
		$dbc = $this->_container->DB;

		// Alternate Search
		$sql = <<<SQL
		SELECT id, fullname, guid, code
		FROM contact
		WHERE (contact.type = 'b2c-client')
		  AND (guid = :g0 OR guid = :g1 OR guid LIKE :g2 OR code = :c0)
		ORDER BY id
		SQL;
		$res_contact = $dbc->fetchAll($sql, [
			':g0' => $guid0,
			':g1' => $guid1,
			':g2' => sprintf('%%%s', substr($guid1, -6)),
			':c0' => $code0,
		]);

		$Contact = [];

		switch (count($res_contact)) {
			case 0:
				return [
					'code' => 404,
					'data' => [],
				];
				// Create
				// $Contact = new Contact($dbc);
				// $Contact['id'] = ULID::create();
				// $Contact['stat'] = 100;
				// $Contact['guid'] = $_POST['client-contact-govt-id'];
				// $Contact['hash'] = '-';
				// $Contact['type'] = 'b2c-client';
				// $Contact['fullname'] = $_POST['client-contact-name'];
				// $Contact['meta'] = json_encode([
				// 	'dob' => $_POST['client-contact-dob']
				// ]);
				// $Contact['dob'] = $_POST['client-contact-dob'];
				// $Contact->save();
				break;
			case 1:
				// Perfect
				$Contact = new Contact(null, $res_contact[0]);
				break;
			default:
				return [
					'code' => 302,
					'data' => $res_contact,
				];
				break;
		}

		return [
			'code' => 200,
			'data' => $Contact,
		];

	}

	/**
	 * Search Metrc
	 */
	function _contact_search_usa_mt($RES)
	{
		throw new \Exception('Not Implemented', 501);

		// $obj = [
		// 	'LicenseNumber' => '000001',
		// 	'LicenseEffectiveStartDate' => date('Y-m-d'), // '2015-06-21',
		// 	'LicenseEffectiveEndDate' => date('Y-m-d', time() + (86400 * 356)),
		// 	'RecommendedPlants' => '6',
		// 	'RecommendedSmokableQuantity' => '2.0',
		// 	'FlowerOuncesAllowed' => null,
		// 	'ThcOuncesAllowed' => null,
		// 	'ConcentrateOuncesAllowed' => null,
		// 	'InfusedOuncesAllowed' => null,
		// 	'MaxFlowerThcPercentAllowed' => null,
		// 	'MaxConcentrateThcPercentAllowed' => null,
		// 	'HasSalesLimitExemption' => false,
		// 	'ActualDate' => date('Y-m-d'),
		// ];

		// $cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		// $cre->setLicense($_SESSION['License']);
		// $res = $cre->contact()->create($obj);

		// var_dump($res);

		// exit;

	}

	function _contact_search_usa_nm_med($RES)
	{
		$dbc = $this->_container->DB;
		$rdb = $this->_container->Redis;

		// $cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		// $cre->setLicense($_SESSION['License']);
		// // $req0 = $cre->_curl_init('');
		// $res = $cre->_curl_exec([
		// 	'action' => 'card_lookup',
		// 	'card_id' => $_POST['client-contact-govt-id'],
		// ]);
		// var_dump($res);

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
			// It inidicates it's valid for like 7 days
			//
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

		$res = $ghc->post('v1/patient/lookup', [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $sid)
			],
			'json' => [
				'LocationLicense' => $_SESSION['License']['code'],
				'CardID' => $_POST['client-contact-govt-id'],
			]
		]);

		$res = $res->getBody()->getContents();
		$res = json_decode($res);
		if (empty($res->CardID)) {
		// 	throw new \Exception('Cannot Find Contact', 500);
			Session::flash('fail', 'Cannot find Contact');
			return $RES->withRedirect('/pos');
		}
		// var_dump($res);

		$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
		if ( ! $Contact->loadBy('guid', $res->CardID)) {
			$Contact['id'] = ULID::create();
			$Contact['stat'] = 100;
			$Contact['guid'] = $res->CardID;
			$Contact['hash'] = '-';
			$Contact['type'] = 'B2C/CLIENT/MED'; // MED/QP or MED/CG == QUalified Patient or Caregiver
			$Contact['fullname'] = '';
			$Contact['meta'] = json_encode($res);
			$Contact->save('Contact/Create in POS by User');
		}

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);
		$Cart->type = 'MED';
		$Cart->Contact = $Contact->toArray();
		$Cart->save();

		return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));

	}

	/**
	 * Search a Contact in Oklahoma
	 */
	function _contact_search_usa_ok($RES)
	{
		$oid = $_POST['client-contact-govt-id'];
		$_SESSION['Cart']['contact-search'] = $oid;

		$url = sprintf('https://omma.us.thentiacloud.net/rest/public/patient-verify/search/?licenseNumber=%s&_=%d'
			, $oid
			, time()
		);
		$req = __curl_init($url);
		$res = curl_exec($req);
		if (empty($res)) {
			$_SESSION['Cart']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Manually [PCO-249]');
			Session::flash('fail', 'Visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}
		$res = json_decode($res, true);

		if (empty($res['result'])) {
			$_SESSION['Cart']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Again [PCO-256]');
			Session::flash('fail', 'Or visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}

		if (empty($res['result']['licenseNumber'])) {
			$_SESSION['Cart']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Again [PCO-262]');
			Session::flash('fail', 'Or visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}

		if ($oid != $res['result']['licenseNumber']) {
			$_SESSION['Cart']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Invalid ID, Please Try Again [PCO-268]');
			Session::flash('fail', 'Or visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}

		$Contact0 = $res['result'];

		$dbc = $this->_container->DB;

		$Contact1 = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
		if ( ! $Contact1->loadBy('guid', $Contact0['licenseNumber'])) {
			$Contact1['id'] = ULID::create();
			$Contact1['stat'] = 100;
			$Contact1['guid'] = $Contact0['licenseNumber'];
			$Contact1['hash'] = '-';
			$Contact1['type'] = 'b2c-client';
			$Contact1['fullname'] = ''; // $_POST['client-contact-name'];
			$Contact1['meta'] = json_encode([
				'@cre' => $Contact0
			]);
			$Contact1->save('Contact/Create in POS by User');
			Session::flash('info', 'New Contact! Please add necessary details');
		};

		$Cart->Contact = $Contact1->toArray();
		$Cart->save();

		return $RES->withRedirect(sprintf('/pos?cart=%s', $Cart->id));

	}

	/**
	 * Send the Contact to the CRE?
	 */
	function _client_contact_to_cre($Contact)
	{
		// switch ($_SESSION[''])
		// $cre = \OpenTHC\CRE::factory($_SESSION['cre']);
		// $cre->setLicense($_SESSION['License']);

		// $res = $cre->contact()->search($_POST['client-contact-pid']);

		// $res = $cre->contact()->single($Contact['guid']);

		// $_POST['client-contact-pid'] = '12-345-678-DD';

		// $res = $cre->contact()->create([
		// 	'LicenseNumber' => $_POST['client-contact-pid'],
		// 	// "LicenseEffectiveStartDate": "2015-06-21",
		// 	// "LicenseEffectiveEndDate": "2016-06-15",
		// 	// "RecommendedPlants": 6,
		// 	// "RecommendedSmokableQuantity": 2.0,
		// 	// "FlowerOuncesAllowed": null,
		// 	// "ThcOuncesAllowed": null,
		// 	// "ConcentrateOuncesAllowed": null,
		// 	// "InfusedOuncesAllowed": null,
		// 	// "MaxFlowerThcPercentAllowed": null,
		// 	// "MaxConcentrateThcPercentAllowed": null,
		// 	// "HasSalesLimitExemption": false,
		// 	// "ActualDate": "2015-12-15"
		// ]);
		// var_dump($res);
		// exit;
	}
}
