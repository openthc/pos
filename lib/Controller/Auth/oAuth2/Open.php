<?php
/**
 *
 */

namespace App\Controller\Auth\oAuth2;

class Open extends \App\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		$cfg = \OpenTHC\Config::get('openthc_sso');

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

		$arg = array(
			'scope' => $cfg['scope'],
		);
		$url = $p->getAuthorizationUrl($arg);

		// Get the state generated for you and store it to the session.
		$_SESSION['oauth2-state'] = $p->getState();

		// return $RES->withRedirect($url);

		$data = array(
			'Page' => array('title' => 'Authenticate'),
			'open_url' => $url,
		);

		return $this->_container->view->render($RES, 'page/auth/open.html', $data);

	}

}
