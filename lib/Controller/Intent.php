<?php
/**
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller;

class Intent extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Intent' ],
		];

		switch ($_GET['a']) {
			case 'delivery-auth':
				return $this->delivery_auth($RES);
				break;
			case 'vendor-view':
				return $RES->write( $this->render('intent/vendor-view.php', $data) );
				break;
		}

		return $RES->write( $this->render('intent/main.php', $data) );

	}

	/**
	 *
	 */
	function delivery_auth($RES)
	{
		if (empty($_SESSION['intent-delivery-username'])) {
			$_SESSION['intent-delivery-username'] = 'test+usa-wa-s@openthc.dev';
		}

		if ('auth-code' == $_POST['a']) {

			// \\CSRF::verify($_POST['CSRF']);

			// Company Database
			$dbc_auth = _dbc('auth');
			$dsn = $dbc_auth->fetchOne('SELECT dsn FROM auth_company WHERE id = :c0', [ ':c0' => $_GET['c'] ]);
			if (empty($dsn)) {
				_exit_html_warn('<h1>Invalid Request [LCI-025]</h1>', 400);
			}

			// Contact Search by PIN
			$dbc = _dbc($dsn);
			$hash = md5(preg_replace('/[^\d]+/', '', $_POST['code']));
			$chk = $dbc->fetchRow('SELECT id, username FROM auth_contact WHERE username = :c1 AND passcode = :p1', [
				':c1' => $_SESSION['intent-delivery-username'],
				':p1' => $hash,
			]);
			if (empty($chk['id'])) {
				_exit_html_warn('<h1>Invalid Request [LCI-035]</h1>', 400);
			}
			// We should have a cookie, with encrypted stuff?
			// Some kind of ID because we need to have unique passcode Too

			// Long-Term Marking as Delivery Auth OK?
			if ($_COOKIE['pos-contact']) {
				// Do Something Smart?
			}

			$_SESSION['Company'] = [];
			$_SESSION['Contact'] = [];
			$_SESSION['License'] = [];

			$_SESSION['Company']['id'] = $_GET['c'];
			$_SESSION['License']['id'] = $_GET['l'];
			$_SESSION['Contact']['id'] = $chk['id'];

			// $tok = $dbc_auth->insert('auth_context_ticket', [
			// 	'id' => _random_hash(),
			// 	'meta' => json_encode([
			// 		'intent' => 'auth-open-delivery',
			// 		'company' => $_GET['c'],
			// 		'contact' => $chk['id'],
			// 	])
			// ]);

			return $RES->withRedirect(sprintf('/auth/init?_=%s&r=/delivery/live', $tok));

		}

		return $RES->write( $this->render('intent/delivery-auth.php', $data) );

	}
}
