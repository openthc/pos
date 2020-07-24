<?php
/**
 * Inbound Connection from Registered Application
 */

namespace App\Controller\Auth;

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

		return $RES->withRedirect('/auth/init');

	}

}
