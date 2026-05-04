<?php
/**
 * Open oAuth2 authentication process
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth\oAuth2;

class Open extends \OpenTHC\Controller\Auth\oAuth2
{
	use \OpenTHC\POS\Traits\OpenAuthBox;
	use \OpenTHC\Traits\FindService;
	use \OpenTHC\Traits\FindContact;
	use \OpenTHC\Traits\FindCompany;

	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// Clear Session
		session_regenerate_id(true);
		$_SESSION = [];

		$ret_path = $this->_get_return_path();

		if ( ! empty($_GET['_'])) {

			$box = $_GET['_'];

			if (preg_match('/^v2024\/([\w\-]{43})\/([\w\-]+)$/', $box, $m)) {

				$this->dbc = _dbc('auth');
				$act = $this->open_auth_box($m[1], $m[2]);
				// These assert if they fail
				$Service = $this->findService($this->dbc, $act->pk);
				$Contact = $this->findContact($this->dbc, $act->contact);
				$Company = $this->findCompany($this->dbc, $act->company);

				$_SESSION['Contact'] = $Contact;
				$_SESSION['Company'] = $Company;
				$_SESSION['License'] = [
					'id' => $act->license
				];

				$url = '/auth/init?' . http_build_query([ 'r' => $ret_path ]);

				return $RES->withRedirect($url);

			}

			__exit_text('Invalid Request [AOO-050]', 400);
		}

		if ( ! empty($_GET['jwt'])) {
			throw new \Exception('@deprecated');
		}

		$p = $this->getProvider($ret_path);
		$url = $p->getAuthorizationUrl([
			'scope' => 'contact company license pos',
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
