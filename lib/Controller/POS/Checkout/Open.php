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
			case 'client-contact-search':
				return $this->contact_search($RES);
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

		$code0 = $_POST['client-contact-pid'];

		$guid0 = $_POST['client-contact-govt-id'];
		$guid1 = $_POST['client-contact-govt-id'];
		if (preg_match('/(\w+)\s*\/\s*(\w+)/', $guid0, $m)) {
			$guid1 = $m[2];
		}

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
				// Create
				$Contact = new Contact($dbc);
				$Contact['id'] = _ulid();
				$Contact['guid'] = $_POST['client-contact-govt-id'];
				$Contact['hash'] = '-';
				$Contact['type'] = 'b2c-client';
				$Contact['fullname'] = $_POST['client-contact-name'];
				$Contact['meta'] = json_encode([
					'dob' => $_POST['client-contact-dob']
				]);
				// $Contact['dob'] = $_POST['client-contact-dob'];
				$Contact->save();
				break;
			case 1:
				// Perfect
				$Contact = new Contact(null, $res_contact[0]);
				break;
			default:
				// Chooser?
				break;
		}

		if (empty($Contact['id'])) {
			Session::flash('fail', 'Cannot find Client Contact');
			return $RES->withRedirect('/pos');
		}

		$_SESSION['Checkout']['Contact'] = $Contact->toArray();

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
