<?php
/**
 * Shop Checkout
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Shop;

class Checkout extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = $this->data;

		$key = sprintf('b2b-sale-%s', $_GET['o']);
		$data['b2b_sale'] = $_SESSION[$key];
		$data['Page']['title'] = sprintf('Checkout :: %s', $data['b2b_sale']['company']['name']);

		$html = $this->render('shop/checkout.php', $data);

		return $RES->write($html);
	}

	/**
	 *
	 */
	function done($REQ, $RES, $ARG)
	{
		$data = $this->data;

		// @todo Lookup Company & Order
		$dbc_auth = _dbc('auth');
		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $_GET['c'] ]);
		if (empty($Company['id'])) {
			_exit_html_fail('<h1>Invalid Request [CSC-037]</h1>', 500);
		}

		$data['Page']['title'] = sprintf('Checkout :: %s', $Company['name']);

		$dbc_user = _dbc($Company['dsn']);
		$rec = $dbc_user->fetchRow('SELECT * FROM b2c_sale_hold WHERE id = :pk', [ ':pk' => $_GET['o'] ]);
		$rec['meta'] = json_decode($rec['meta'], true);
		$data['b2c'] = $rec['meta'];

		$html = $this->render('shop/checkout-done.php', $data);

		return $RES->write($html);

	}

	/**
	 *
	 */
	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
			case 'cart-commit':

				$_POST['contact-email'] = filter_var($_POST['contact-email'], FILTER_VALIDATE_EMAIL);
				if (empty($_POST['contact-email'])) {
					_exit_html_warn('<h1>Invalid Email Address [CSC-057]</h1><h2>Please go back and correct</h2>', 400);
				}
				$_POST['contact-phone'] = _phone_e164($_POST['contact-phone']);


				$b2b = $_SESSION[sprintf('b2b-sale-%s', $_GET['o'])];
				$b2b['contact'] = [
					'name' => $_POST['contact-name'],
					'email' => $_POST['contact-email'],
					'phone' => $_POST['contact-phone']
				];

				// Company
				$dbc_auth = _dbc('auth');
				$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $b2b['company']['id'] ]);
				if (empty($Company['id'])) {
					_exit_html_fail('<h1>Invalid Request [CSC-063]</h1>', 500);
				}

				$dbc_user = _dbc($Company['dsn']);
				unset($Company['dsn']);

				// Lookup Contact
				$chk = $dbc_user->fetchOne('SELECT id FROM contact WHERE email = :e', [ ':e' => $b2b['contact']['email'] ]);
				if (empty($chk)) {
					$chk = $dbc_user->fetchOne('SELECT id FROM contact WHERE phone = :p', [ ':p' => $b2b['contact']['phone'] ]);
				}
				if (empty($chk)) {

					// Search Global Directory?
					// $dir = new \OpenTHC\Service\OpenTHC('dir');
					// $chk = $dir->get(sprintf('/api/contact/search?q=%s', rawurlencode($_POST['contact-email'])));
					$chk = _ulid();
					$rec = [
						'id' => $chk,
						'type' => 'client',
						'fullname' => $b2b['contact']['name'],
						'email' => $b2b['contact']['email'],
						'phone' => $b2b['contact']['phone'],
					];
					$rec['hash'] = md5(json_encode($rec));
					$rec['guid'] = $rec['id'];
					$dbc_user->insert('contact', $rec);

				}

				$Contact = [
					'id' => $chk
				];

				// Insert POS Hold
				$dbc_user->insert('b2c_sale_hold', [
					'id' => $b2b['id'],
					'contact_id' => $Contact['id'],
					'type' => 'online',
					'stat' => 100,
					'meta' => json_encode($b2b),
				]);

				unset($_SESSION[sprintf('b2b-sale-%s', $b2b['id'])]);
				unset($_SESSION[sprintf('cart-%s', $Company['id'])]);

				break;

		}

		// Redirect
		return $RES->withRedirect(sprintf('/shop/checkout/done?c=%s&o=%s'
			, $b2b['company']['id']
			, $b2b['id']
		));

	}

}
