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

		$Company = array_merge($Company0, $Company1);
		$cre_meta = json_decode($Company['cre_meta'], true);
		unset($Company['cre_meta']);

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
		$cre = \OpenTHC\CRE::getConfig($Company['cre']);
		$cre['license-sk'] = $cre_meta['license-sk'] ?: $cre_meta['license-key'];
		$_SESSION['cre'] = $cre;

		return $RES->withRedirect($ret);

	}
}
