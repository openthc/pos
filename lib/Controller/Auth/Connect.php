<?php
/**
 * Inbound Connection from Registered Application
 */

namespace App\Controller\Auth;

use Edoceo\Radix\DB\SQL;

class Connect extends \OpenTHC\Controller\Auth\Connect
{
	function __invoke($REQ, $RES, $ARG)
	{
		$RES = parent::__invoke($REQ, $RES, $ARG);

		$x = $RES->getStatusCode();
		switch ($x) {
		case 200:
		case 301:
		case 302:
			// OK
			break;
		default:
			return $RES;
		}

		if (empty($_SESSION['Company']['id']) || empty($_SESSION['License']['id']) || empty($_SESSION['Contact']['id'])) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Session State [CAC#029]'],
			], 500);
		}

		// Lookup the DSN
		$cfg = \OpenTHC\Config::get('database_auth'); // @todo shold be AUTH
		if (empty($cfg)) {
			return $RES->withJSON([
				'data' => [],
				'meta' => [ 'detail' => 'Fatal Database Error [CAC#032]'],
			], 500);
		}
		$dbc = new SQL(sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']), $cfg['username'], $cfg['password']);
		$C1 = $dbc->fetchRow('SELECT * FROM auth_company WHERE ulid = ?', $_SESSION['Company']['id']);
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

		return $RES->withRedirect('/dashboard');

	}

}
