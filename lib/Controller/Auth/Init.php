<?php
/**
 * Initialise an Authenticated Session
 */

namespace App\Controller\Auth;

class Init extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$ret = $_GET['r'];
		if (empty($ret)) {
			$ret = '/dashboard';
		}

		// Lookup the DSN
		$dbc_auth = _dbc('auth');
		$chk = $dbc_auth->fetchRow('SELECT dsn, cre, cre_meta FROM auth_company WHERE id = ?', $_SESSION['Company']['id']);
		if (empty($chk['dsn'])) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Database Error [CAC-043]'],
			], 500);
		}

		$dbc_user = _dbc($chk['dsn']);
		$_SESSION['dsn'] = $chk['dsn'];
		unset($_SESSION['Company']['dsn']);

		// Find the Default License?
		if (empty($_SESSION['License'])) {
			// Find It?
			$License = $dbc_user->fetchRow('SELECT * FROM license WHERE flag & :f1 = :f1 ORDER BY id LIMIT 1', [ ':f1' => 0x01000000 ]);
			$_SESSION['License'] = $License;
		}

		// Save the CRE Stuff?
		if (!empty($chk['cre_meta'])) {
			$_SESSION['cre'] = json_decode($chk['cre_meta'], true);
		}

		// Cleanup some CRE Data
		if (empty($_SESSION['cre']['service-key']) && !empty($_SESSION['cre']['program-key'])) {
			$_SESSION['cre']['service-key'] = $_SESSION['cre']['program-key'];
			unset($_SESSION['cre']['program-key']);
		}

		if (empty($_SESSION['cre']['license']) && !empty($_SESSION['cre']['auth']['license'])) {
			$_SESSION['cre']['license'] = $_SESSION['cre']['auth']['license'];
			unset($_SESSION['cre']['auth']['license']);
		}

		if (empty($_SESSION['cre']['license-key']) && !empty($_SESSION['cre']['auth']['license-key'])) {
			$_SESSION['cre']['license-key'] = $_SESSION['cre']['auth']['license-key'];
			unset($_SESSION['cre']['auth']['license-key']);
		}

		unset($_SESSION['cre']['auth']);

		return $RES->withRedirect($ret);

	}
}
