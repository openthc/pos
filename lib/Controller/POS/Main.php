<?php
/**
 * POS Main
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS;

use Edoceo\Radix\Session;
use Edoceo\Radix\ULID;

use OpenTHC\Contact;

class Main extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$sql = <<<SQL
		SELECT count(id)
		FROM inventory_full
		WHERE license_id = :l0
			AND stat = 200
			AND qty > 0
			AND sell IS NOT NULL
			AND sell > 0
		SQL;
		$arg = [
			':l0' => $_SESSION['License']['id']
		];
		$chk = $dbc->fetchOne($sql, $arg);
		if (empty($chk)) {
			_exit_html_fail('<h1>Inventory Lots need to be present and priced for the POS to operate [CPH-020]</h1>', 501);
		}

		if (empty($_SESSION['pos-terminal-id'])) {
			$_SESSION['pos-terminal-id'] = ULID::create();
		}

		if (empty($_SESSION['pos-terminal-contact'])) {
			$data = [];
			$data['Page'] = [ 'title' => 'Terminal Authentication'];

			$contact_select_list = [];
			$sql = <<<SQL
			SELECT id, fullname
			FROM contact
			WHERE type IN ('root', 'EMPLOYEE')
			AND stat = 200
			-- AND ...
			ORDER BY fullname
			SQL;
			$data['seller_contact_list'] = $dbc->fetchAll($sql);

			return $RES->write( $this->render('pos/open.php', $data) );

		}

		// Page Data
		$data = [];
		$data['Page'] = [];
		$data['Page']['title'] = sprintf('POS :: %s <code>%s</code>', $_SESSION['License']['name'], $_SESSION['License']['code']);
		$data['Page']['title'] = sprintf('POS :: %s <code>%s</code>', $_SESSION['License']['name'], $_SESSION['License']['code']);

		$Cart = new \OpenTHC\POS\Cart($this->_container->Redis, $_GET['cart']);

		$data['cart'] = $Cart;

		if (empty($Cart->Contact)) {
			return $RES->write( $this->render('pos/contact-select.php', $data) );
		}

		if ($Cart->Contact->stat != Contact::STAT_LIVE) {
			return $RES->write( $this->render('pos/contact-verify.php', $data) );
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

			if (empty($_POST['seller-contact-id'])) {
				throw new \Exception('Invalid Request [CPM-095]', 500);
			}

			// Lookup Contact by this Auth Code
			$dbc = $this->_container->DB;

			$sql = <<<SQL
			SELECT *
			FROM auth_contact
			WHERE id = :c0 AND auth_code = :a0
			SQL;
			$Contact = $dbc->fetch_row($sql, [
				':c0' => $_POST['seller-contact-id'],
				':a0' => $_POST['code'],
			]);
			if (empty($Contact['id'])) {
				Session::flash('fail', 'Invalid Contact');
				return $RES->withRedirect('/pos');
			}

			// Assign to Register Session
			// Set Expiration in T minutes?
			// $R = $this->_container->Redis;
			// $k = sprintf('/%s/pos-terminal', $_SESSION['Contact']['id']);
			// $v = $_SESSION['Contact']['id'];
			// $R->set($k, $v, [ 'ex' => 600 ]);

			$_SESSION['pos-terminal-contact'] = $_SESSION['Contact']['id'];

			return $RES->withRedirect('/pos');

			break;

		}

	}

	private function openCart()
	{
	}

}
