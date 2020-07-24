<?php
/**
 * Initialize The Session
 */

namespace App\Controller\Auth;

use Edoceo\Radix\DB\SQL;

class Init extends \App\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		// @todo Some Logging?

		$ret = $_GET['r'];
		if (empty($ret)) {
			$ret = '/home';
		}

		// Lookup the DSN
		$cfg = \OpenTHC\Config::get('database_auth');
		if (empty($cfg)) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Database Error [CAC#032]'],
			], 500);
		}
		$dbc_auth = new SQL(sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']), $cfg['username'], $cfg['password']);
		$C1 = $dbc_auth->fetchRow('SELECT * FROM auth_company WHERE ulid = ?', $_SESSION['Company']['id']);
		if (empty($C1['dsn'])) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Database Error [CAC#043]'],
			], 500);
		}

		$_SESSION['dsn'] = $C1['dsn']; // $_SESSION['Company']['dsn'];

		unset($_SESSION['Company']['dsn']);
		unset($_SESSION['cre']);
		unset($_SESSION['cre-auth']);
		unset($_SESSION['cre-base']);
		unset($_SESSION['gid']);
		unset($_SESSION['uid']);
		unset($_SESSION['sql-hash']);

		return $RES->withRedirect($ret);

	}
}
