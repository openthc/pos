<?php
/**
 *
 */

namespace App\Controller\Auth\oAuth2;

class Open extends \App\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		// Clear Session
		$key_list = array_keys($_SESSION);
		foreach ($key_list as $k) {
			unset($_SESSION[$k]);
		}

		$r = $_GET['r'];
		switch ($r) {
		case '1':
		case 'r':
			$r = $_SERVER['HTTP_REFERER'];
			break;
		}

		$p = $this->getProvider($r);
		$_SESSION['oauth2-state'] = $p->getState();

		$cfg = \OpenTHC\Config::get('openthc_sso');
		$arg = array(
			'scope' => $cfg['scope'],
		);
		$url = $p->getAuthorizationUrl($arg);

		return $RES->withRedirect($url);

	}

}
