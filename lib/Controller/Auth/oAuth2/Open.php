<?php
/**
 * Open oAuth2 authentication process
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Auth\oAuth2;

class Open extends \OpenTHC\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		// Clear Session
		$key_list = array_keys($_SESSION);
		foreach ($key_list as $k) {
			unset($_SESSION[$k]);
		}

		$ret = $this->_get_return_path();

		$p = $this->getProvider($ret);
		$url = $p->getAuthorizationUrl([
			'scope' => 'pos company contact',
		]);

		$_SESSION['oauth2-state'] = $p->getState();

		return $RES->withRedirect($url);

	}

	/**
	 *
	 */
	function _get_return_path()
	{
		$ret = '/dashboard';
		if (!empty($_GET['r'])) {
			switch ($_GET['r']) {
				case '1':
				case 'r':
					// @todo should validate the referrer
					$ret = $_SERVER['HTTP_REFERER'];
					break;
				default:
					$ret = $_GET['r'];
					break;
			}
		}

		return $ret;

	}

}
