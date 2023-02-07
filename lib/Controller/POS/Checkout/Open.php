<?php
/**
 * Open the Checkout Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Checkout;

use Edoceo\Radix\Session;

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
				return $this->contact_open($RES);
			case 'client-contact-search':
				return $this->contact_search($RES);
			case 'client-contact-skip':
				$_SESSION['Checkout']['Contact'] = [
					'id' => '018NY6XC00C0NTACT000WALK1N',
					'name' => 'Walk In',
				];
				return $RES->withRedirect('/pos');
			case 'client-contact-update':
				return $this->contact_open($RES);
			default:
				Session::flash('fail', 'Invalid Requset [PCO-045]');
				return $RES->withRedirect('/pos');
		}
	}

	/**
	 *
	 */
	function contact_open($RES)
	{
		$dbc = $this->_container->DB;

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
			case 'usa/ok':
				$cre = \OpenTHC\CRE::factory($_SESSION['cre']);
				$cre->setLicense($_SESSION['License']);
				$res = $cre->contact()->single($guid1);
				switch ($res['code']) {
					case 200:
						$Contact = new Contact($dbc); // , [ 'guid' => $res['data']['PatientId'] ]);
						if ( ! $Contact->loadBy('guid', $res['data']['PatientId'])) {
							$Contact['id'] = _ulid();
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
						Session::flash('fail', $cre->formatError($res));
						return $RES->withRedirect('/pos');
				}
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
				// $Contact['id'] = _ulid();
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
