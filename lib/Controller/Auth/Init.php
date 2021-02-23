<?php
/**
 * Initialize The Session
 */

namespace App\Controller\Auth;

use Edoceo\Radix\DB\SQL;

class Init extends \App\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		// @todo Some Logging?

		$ret = $_GET['r'];
		if (empty($ret)) {
			$ret = '/dashboard';
		}

		// Lookup the DSN
		$dbc_auth = _dbc('auth');
		$dsn = $dbc_auth->fetchOne('SELECT dsn FROM auth_company WHERE id = ?', $_SESSION['Company']['id']);
		if (empty($dsn)) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Database Error [CAC-043]'],
			], 500);
		}

		$_SESSION['dsn'] = $dsn;

		unset($_SESSION['Company']['dsn']);
		unset($_SESSION['cre']);
		unset($_SESSION['cre-auth']);
		unset($_SESSION['cre-base']);

		return $RES->withRedirect($ret);

	}
}
