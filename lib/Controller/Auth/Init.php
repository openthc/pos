<?php
/**
 * Initialize The Session
 */

namespace App\Controller\Auth;

class Init extends \App\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		// @todo Some Logging?

		$r = $_GET['r'];
		if (empty($r)) {
			$r = '/dashboard';
		}

		return $RES->withRedirect($r);

	}
}
