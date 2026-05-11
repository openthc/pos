<?php
/**
 * Initialise an Authenticated Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth;

class Pick extends \OpenTHC\Controller\Base
{
	use \OpenTHC\Traits\FindLicense;

	/**
	 *
	 */
	function license($REQ, $RES, $ARG)
	{
		$data = [];
		$data['Page'] = [];
		$data['Page']['title'] = 'POS / Auth / Pick License';

		$dbc_user = _dbc($_SESSION['dsn']);

		$sql = <<<SQL
		SELECT *
		FROM license
		WHERE flag & :f1 = :f1
		ORDER BY code, name
		SQL;
		$data['license_list'] = $dbc_user->fetchAll($sql, [
			':f1' => 0x01000000
		]);

		// No Menu!
		$data['menu-zero'] = 'mini';

		return $RES->write( $this->render('/auth/pick-license.php', $data) );
	}

	/**
	 * Accept the Selected License ID
	 */
	function license_post($REQ, $RES, $ARG)
	{
		// Check Valid Session ?
		// Perhaps an ACL Check?

		switch ($_POST['a']) {
			case 'license-select':
				$_SESSION['License'] = [];
				$_SESSION['License']['id'] = $_POST['license-id'];
				return $RES->withRedirect('/auth/init');
		}

		throw new \Exception('Invalid Request [CAP-056]', 500);

	}

}
