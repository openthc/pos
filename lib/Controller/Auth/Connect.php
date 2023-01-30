<?php
/**
 * Inbound Connection from Registered Application
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Auth;

class Connect extends \OpenTHC\Controller\Auth\Connect
{
	function __invoke($REQ, $RES, $ARG)
	{
		// Requires License
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
				'meta' => [ 'detail' => 'Fatal Session State [CAC-029]'],
			], 500);
		}

		// Keep CRE Data
		$_SESSION['cre'] = $this->_connect_info['cre'];

		return $RES->withRedirect('/auth/init');

	}

}
