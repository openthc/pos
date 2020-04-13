<?php
/**
 *
 */

namespace App\Controller\Auth;

class oAuth2 extends \OpenTHC\Controller\Base
{
	/**
		Verify the State or DIE
	*/
	function checkState()
	{
		$a = $_SESSION['oauth2-state'];
		$b = $_GET['state'];

		// unset($_SESSION['oauth2-state']);

		if (empty($a)) {
			_exit_text('Invalid State [CAO#024]');
		}

		if (empty($b)) {
			_exit_text('Invalid State [CAO#030]', 400);
		}

		if ($a != $b) {
			_exit_text('Invalid State [CAO#036]', 400);
		}
	}

	/**
		Return the oAuth Provider
	*/
	protected function getProvider($r=null)
	{
		$cfg = \OpenTHC\Config::get('openthc_sso');

		$u = sprintf('https://%s/auth/back?%s', $_SERVER['SERVER_NAME'], http_build_query(array('r' => $r)));
		$u = trim($u, '?');
		$p = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId' => $cfg['client'],
			'clientSecret' => $cfg['secret'],
			'redirectUri' => $u,
			'urlAuthorize' => $cfg['url'] . '/oauth2/authorize',
			'urlAccessToken' => $cfg['url'] . '/oauth2/token',
			'urlResourceOwnerDetails' => $cfg['url'] . '/oauth2/profile',
			'verify' => true
		]);

		return $p;
	}
}
