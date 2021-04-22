<?php
/**
 * Make Sure We're Authenticated
 */

namespace App\Middleware;

class Auth extends \OpenTHC\Middleware\Base
{
	function __invoke($REQ, $RES, $NMW)
	{
		// Session Good?
		if (empty($_SESSION['Company']['id']) || empty($_SESSION['Contact']['id'])) {
			// Authenticate
			return $RES->withRedirect('/auth/open');
		}

		$RES = $NMW($REQ, $RES);

		return $RES;
	}

}
