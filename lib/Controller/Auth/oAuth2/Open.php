<?php
/**
 * Open oAuth2 authentication process
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth\oAuth2;

use OpenTHC\JWT;

class Open extends \OpenTHC\Controller\Auth\oAuth2
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// Clear Session
		$key_list = array_keys($_SESSION);
		foreach ($key_list as $k) {
			unset($_SESSION[$k]);
		}

		$ret_path = $this->_get_return_path();

		if ( ! empty($_GET['jwt'])) {

			// $p = $this->getProvider();
			// $sso = new \OpenTHC\Service('sso');
			// $res = $sso->get('/api/jwt/verify?jwt=' . $_GET['jwt']);
			// switch ($res['code']) {
			// 	case 200:
			// 		// OK
			// 	default:
			// 		return $RES->withJSON(['meta' => [ 'note' => 'Invalid Token [AOO-033]' ]], 400);
			// }

			$dbc = _dbc('auth');

			try {

				$chk = JWT::decode_only($_GET['jwt']);
				$key = $dbc->fetchOne('SELECT hash FROM auth_service WHERE id = :s0', [ ':s0' => $chk->body->iss ]);
				$jwt = JWT::verify($_GET['jwt'], $key);

				$_SESSION['Contact'] = [
					'id' => $chk->sub,
				];
				if (empty($_SESSION['Contact']['id'])) {
					return $RES->withJSON(['meta' => [ 'note' => 'Invalid Contact [AOO-035]' ]], 400);
				}

				$_SESSION['Company'] = [
					'id' => $jwt->company,
				];
				if (empty($_SESSION['Company']['id'])) {
					return $RES->withJSON(['meta' => [ 'note' => 'Invalid Company [AOO-042]' ]], 400);
				}

				$_SESSION['License'] = [
					'id' => $chk->license,
				];
				if (empty($_SESSION['License']['id'])) {
					return $RES->withJSON(['meta' => [ 'note' => 'Invalid License [AOO-049]' ]], 400);
				}

				return $RES->withRedirect('/auth/init');

			} catch (\Exception $e) {
				// What?
			}

			return $RES->withRedirect($ret_path);

		}

		$p = $this->getProvider($ret_path);
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
