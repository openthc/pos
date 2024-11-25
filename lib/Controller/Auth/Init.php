<?php
/**
 * Initialise an Authenticated Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth;

class Init extends \OpenTHC\Controller\Base
{
	use \OpenTHC\Traits\FindLicense;

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
			throw new \Exception('Fatal Database Error [CAC-043]', 500);
		}

		if (empty($Company0['cre'])) {
			throw new \Exception('Company Configuration requires CRE [CAC-030]', 500);
		}

		$dbc_user = _dbc($Company0['dsn']);
		$Company1 = $dbc_user->fetchRow('SELECT * FROM auth_company WHERE id = :c0', [ ':c0' => $_SESSION['Company']['id'] ]);
		$_SESSION['dsn'] = $Company0['dsn'];
		unset($Company0['dsn']);

		$_SESSION['Company'] = array_merge($Company0, $Company1);
		$_SESSION['Company']['cre_meta'] = json_decode($_SESSION['Company']['cre_meta'], true);

		// Load License
		// Maybe offer a License Picker?
		// return $RES->withRedirect('/auth/license/select');
		if ( ! empty($_SESSION['License']['id'])) {
			// Reload License
			$_SESSION['License'] = $this->findLicense($dbc_user, $_SESSION['License']['id']);
		} else {
			// Find Default
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
		}

		// Save the CRE Stuff?
		$_SESSION['cre'] = \OpenTHC\CRE::getEngine($_SESSION['Company']['cre']);

		// Cleanup some legacy CRE data
		if (empty($_SESSION['cre']['license']) && !empty($_SESSION['cre']['auth']['license'])) {
			$_SESSION['cre']['license'] = $_SESSION['cre']['auth']['license'];
			unset($_SESSION['cre']['auth']['license']);
		}

		unset($_SESSION['cre']['auth']);

		return $RES->withRedirect($ret);

	}
}
