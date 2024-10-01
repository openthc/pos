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
				$_SESSION['Checkout']['Contact'] = [
					'id' => '018NY6XC00C0NTACT000WALK1N',
					'stat' => 200,
					'name' => 'Walk In',
				];
				return $RES->withRedirect('/pos');
			case 'client-contact-update':
				return $this->contact_open($RES);
			case 'client-contact-update-force':
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
				$_SESSION['Checkout']['Contact'] = $Contact1->toArray();
				return $RES->withRedirect('/pos');
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
		$Contact = new Contact($this->_container->DB, $_SESSION['Checkout']['Contact']);

		$Contact['stat'] = Contact::STAT_LIVE;
		$Contact['fullname'] = $_POST['client-contact-name'];
		$Contact['code'] = $_POST['client-contact-code'];
		$m = $Contact->getMeta();
		$m['client-type'] = $_POST['client-contact-type'];
		$m['dob'] = $_POST['client-contact-dob'];
		$Contact['meta'] = json_encode($m);

		$Contact->save('Contact/Update by User');

		$_SESSION['Checkout']['Contact'] = $Contact->toArray();

		return $RES->withRedirect('/pos');

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

	/**
	 *
	 */
	function contact_open($RES)
	{
		$dbc = $this->_container->DB;

		switch ($_SESSION['cre']['id']) {
			case 'usa/ok':
				// Has to Lookup on External Site
				return $this->_contact_search_usa_ok($RES);
			case 'usa/mt':
				// Has to Lookup on External Site
				return $this->_contact_search_usa_ok($RES);
		}

		// $code0 = $_POST['client-contact-pid'];

		$gov_id = null;
		$gov_id_type = null;
		$guid0 = $_POST['client-contact-govt-id'];
		if (preg_match('/^([\w\- ]{4,32})$/', $guid0)) {
			$gov_id_type = 'PATIENT_ID';
		} elseif (preg_match('/.*ANSI.*/', $guid0)) {
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
							$_SESSION['Checkout']['Contact'] = $Contact->toArray();
							return $RES->withRedirect('/pos');
						}

						break;
					case 404:
						$_SESSION['Checkout']['Contact'] = [
							'id' => '',
							'guid' => $guid1,
							'stat' => 100,
							'type' => 'b2c-client',
						];
						return $RES->withRedirect('/pos');
					default:
						// Session::flash('fail', $cre->formatError($res));
						// return $RES->withRedirect('/pos');
				}
			break;
		case 'deu':
		case 'usa/vt':
		case 'usa/wa':
			$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
			if ( ! $Contact->loadBy('guid', $res['data']['PatientId'])) {
				$Contact['id'] = ULID::create();
				$Contact['stat'] = 100;
				$Contact['guid'] = $guid1;
				$Contact['hash'] = '-';
				$Contact['type'] = 'b2c-client';
				$Contact['fullname'] = '';
				$Contact->save('Contact/Create in POS by User');
			}

			if ( ! empty($Contact['id'])) {
				$_SESSION['Checkout']['Contact'] = $Contact->toArray();
				return $RES->withRedirect('/pos');
			}
			break;
		default:
			Session::flash('warn', sprintf('Unsupported CRE: %s', $_SESSION['cre']['id']));
		}

		if (empty($Contact['id'])) {
			Session::flash('fail', 'Cannot find Client Contact');
			return $RES->withRedirect('/pos');
		}

		$_SESSION['Checkout']['Contact'] = $Contact->toArray();

		return $RES->withRedirect('/pos');

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
					'code' => '302',
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
	 * Search a Contact in Oklahoma
	 */
	function _contact_search_usa_ok($RES)
	{
		$oid = $_POST['client-contact-govt-id'];
		$_SESSION['Checkout']['contact-search'] = $oid;

		$url = sprintf('https://omma.us.thentiacloud.net/rest/public/patient-verify/search/?licenseNumber=%s&_=%d'
			, $oid
			, time()
		);
		$req = __curl_init($url);
		$res = curl_exec($req);
		if (empty($res)) {
			$_SESSION['Checkout']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Manually [PCO-249]');
			Session::flash('fail', 'Visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}
		$res = json_decode($res, true);

		if (empty($res['result'])) {
			$_SESSION['Checkout']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Again [PCO-256]');
			Session::flash('fail', 'Or visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}

		if (empty($res['result']['licenseNumber'])) {
			$_SESSION['Checkout']['contact-push'] = true;
			Session::flash('fail', 'Patient Search Failed, Please Try Again [PCO-262]');
			Session::flash('fail', 'Or visit: <a href="https://omma.us.thentiacloud.net/webs/omma/register/">omma.us.thentiacloud.net/webs/omma/register</a> to perform the lookup');
			return $RES->withRedirect($_SERVER['HTTP_REFERER']);
		}

		if ($oid != $res['result']['licenseNumber']) {
			$_SESSION['Checkout']['contact-push'] = true;
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

		$Contact2 = $Contact1->toArray();

		$_SESSION['Checkout']['Contact'] = $Contact2;

		return $RES->withRedirect('/pos');

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
