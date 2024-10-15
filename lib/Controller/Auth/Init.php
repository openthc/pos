<?php
/**
 * Initialise an Authenticated Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth;

class Init extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$ret = $_GET['r'];
		if (empty($ret)) {
			$ret = '/dashboard';
		}

		// Lookup the DSN
		$dbc_auth = _dbc('auth');
		$Company0 = $dbc_auth->fetchRow('SELECT * FROM auth_company WHERE id = :c0', [ ':c0' => $_SESSION['Company']['id'] ]);
		if (empty($Company0['dsn'])) {
			_exit_html_fail('<h1>Fatal Database Error [CAC-043]</h1>', 500);
		}

		if (empty($Company0['cre'])) {
			_exit_html_fail('<h1>Company Configuration requires CRE [CAC-030]</h1>', 500);
		}

		$dbc_user = _dbc($Company0['dsn']);
		$Company1 = $dbc_user->fetchRow('SELECT * FROM auth_company WHERE id = :c0', [ ':c0' => $_SESSION['Company']['id'] ]);
		$_SESSION['dsn'] = $Company0['dsn'];
		unset($Company0['dsn']);

		$_SESSION['Company'] = array_merge($Company0, $Company1);
		$_SESSION['Company']['cre_meta'] = json_decode($_SESSION['Company']['cre_meta'], true);

		// Find the Default License?
		if (empty($_SESSION['License'])) {
			$sql = <<<SQL
			SELECT *
			FROM license
			WHERE type IN ('Retail', 'MMJ')
			AND flag & :f1 = :f1
			ORDER BY id LIMIT 1
			SQL;
			$License = $dbc_user->fetchRow($sql, [
				':f1' => 0x01000000
			]);
			$_SESSION['License'] = $License;
		} else {
			// Reload License
			$License = $dbc_user->fetchRow('SELECT * FROM license WHERE id = :l0', [ ':l0' => $_SESSION['License']['id'] ]);
			$_SESSION['License'] = $License;
		}

		// Save the CRE Stuff?
		// if (!empty($chk['cre_meta'])) {
		$_SESSION['cre'] = \OpenTHC\CRE::getEngine($_SESSION['Company']['cre']);
		$_SESSION['cre']['license-key'] = $_SESSION['Company']['cre_meta']['license-key'];
		// array_merge($_SESSION['cre'], $cre_meta);
		// }

		// Cleanup some CRE Data
		if (empty($_SESSION['cre']['license']) && !empty($_SESSION['cre']['auth']['license'])) {
			$_SESSION['cre']['license'] = $_SESSION['cre']['auth']['license'];
			unset($_SESSION['cre']['auth']['license']);
		}

		// if (empty($_SESSION['cre']['license-key']) && !empty($_SESSION['cre']['auth']['license-key'])) {
		// 	$_SESSION['cre']['license-key'] = $_SESSION['cre']['auth']['license-key'];
		// 	unset($_SESSION['cre']['auth']['license-key']);
		// }

		unset($_SESSION['cre']['auth']);

		return $RES->withRedirect($ret);

	}
}
