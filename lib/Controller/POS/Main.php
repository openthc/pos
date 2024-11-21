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
			return $RES->write( $this->render('pos/open.php', $data) );
		}

		if (empty($_GET['cart'])) {
			$_SESSION['Cart'] = [];
			$_SESSION['Cart']['id'] = \Edoceo\Radix\ULID::create();
			return $RES->withRedirect(sprintf('/pos?cart=%s', $_SESSION['Cart']['id']));
		}


		$data = array(
			'Page' => array('title' => sprintf('POS :: %s <code>%s</code>', $_SESSION['License']['name'], $_SESSION['License']['code']))
		);

		if (empty($_SESSION['Cart']['Contact'])) {
			return $RES->write( $this->render('pos/contact-select.php', $data) );
		}

		if ($_SESSION['Cart']['Contact']['stat'] != Contact::STAT_LIVE) {
			return $RES->write( $this->render('pos/contact-verify.php', $data) );
		}

		// Get a Cart ID
		if (empty($_GET['cart'])) {
			return $RES->withRedirect(sprintf('/pos?cart=%s', \Edoceo\Radix\ULID::create()));
		}

		$data['cart'] = $_SESSION['cart'];

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
				__exit_text('Invalid Contact', 400);
			}

			// Assign to Register Session
			// Set Expiration in T minutes?
			$R = $this->_container->Redis;
			$k = sprintf('/%s/pos-terminal', $_SESSION['Contact']['id']);
			$v = $_SESSION['Contact']['id'];
			$R->set($k, $v, [ 'ttl' => 600 ]);

			$_SESSION['pos-terminal-contact'] = $_SESSION['Contact']['id'];

			$_SESSION['Cart'] = [];

			return $RES->withRedirect('/pos');

			break;

		}

	}

	private function openCart()
	{
	}

}
