<?php
/**
 * POS Main
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\POS;

use Edoceo\Radix\Session;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$sql = 'SELECT count(id) FROM lot_full WHERE license_id = :l0 AND stat = 200 AND qty > 0 AND sell IS NOT NULL and sell > 0';
		$arg = [
			':l0' => $_SESSION['License']['id']
		];
		$chk = $dbc->fetchOne($sql, $arg);
		if (empty($chk)) {
			_exit_html_fail('<h1>Inventory Lots need to be present and priced for the POS to operate [CPH-020]</h1>', 501);
		}


		if (empty($_SESSION['pos-terminal-id'])) {
			$_SESSION['pos-terminal-id'] = _ulid();
		}

		// if ('auth' == $_GET['v']) {
		if (empty($_SESSION['pos-terminal-contact'])) {
			$data = [];
			$data['Page'] = [ 'title' => 'Terminal Authentication'];
			return $RES->write( $this->render('pos/open.php', $data) );
		}

		if ('scan' == $_GET['v']) {
			$data = [];
			$data['Page'] = [ 'title' => 'ID Scanner'];
			return $RES->write( $this->render('pos/scan-id.php', $data) );
		}

		$data = array(
			'Page' => array('title' => 'POS :: #' . $_SESSION['pos-terminal-id']),
		);

		if (empty($_SESSION['Cart']['Contact'])) {
			return $RES->write( $this->render('pos/terminal/contact.php', $data) );
		}

		return $RES->write( $this->render('pos/terminal/main.php', $data) );

	}

	/**
	 * POST Handler
	 */
	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'auth-code':

			// Lookup Contact by this Auth Code
			$code = $_POST['code'];

			$dbc = $this->_container->DB;

			$Contact = $dbc->fetch_row('SELECT * FROM auth_contact WHERE auth_code = :a0', [
				':a0' => $_POST['code'],
			]);
			if ( ! empty($Contact['id'])) {

			}

			// Assign to Register Session
			// Set Expiration in T minutes?
			$R = $this->_container->Redis;
			$k = sprintf('/%s/pos-terminal', $_SESSION['Contact']['id']);
			$v = $_SESSION['Contact']['id'];
			$R->set($k, $v, [ 'ttl' => 600 ]);

			$_SESSION['pos-terminal-contact'] = $_SESSION['Contact']['id'];

			return $RES->withRedirect('/pos');

			break;

		case 'client-contact-update':

			$dbc = $this->_container->DB;
			$Contact = $dbc->fetchRow('SELECT * FROM contact WHERE code = :c0', [
				':c0' => $_POST['client-contact-pid'],
			]);
			if (empty($Contact['id'])) {

				$Contact = [
					'id' => _ulid(),
					'code' => $_POST['client-contact-pid'],
					'guid' => $_POST['client-contact-pid'],
					'stat' => '100',
					'type' => 'client',
					'fullname' => $_POST['client-contact-pid'],
					'hash' => '-',
				];

				$dbc->insert('contact', $Contact);

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

			$_SESSION['Cart']['Contact'] = $Contact;

			return $RES->withRedirect('/pos');

		}

	}

	private function openCart()
	{
	}

}
