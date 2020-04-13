<?php
/**
 * SSO Bounces Back Here
 */

namespace App\Controller\Auth\oAuth2;

class Back extends \App\Controller\Auth\oAuth2
{
	function __invoke($REQ, $RES, $ARG)
	{
		$p = $this->getProvider();

		if (empty($_GET['code'])) {
			_exit_text('Invalid Link [AOB#022]', 400);
		}

		// Check State
		$this->checkState();

		// Try to get an access token using the authorization code grant.
		try {
			$tok = $p->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
		} catch (\Exception $e) {
			_exit_text('Invalid Access Token [AOB#027]', 400);
		}

		if (empty($tok)) {
			_exit_text('Invalid Access Token [AOB#031]', 400);
		}

		//echo 'Access Token: ' . $res->getToken() . "\n";
		//echo 'Refresh Token: ' . $res->getRefreshToken() . "\n";
		//echo 'Expired in: ' . $res->getExpires() . "\n";
		//echo 'Already expired? ' . ($res->hasExpired() ? 'expired' : 'not expired') . "\n";
		$tok_a = json_decode(json_encode($tok), true);

		if (empty($tok_a['access_token'])) {
			_exit_text('Invalid Access Token [AOB#041]', 400);
		}

		if (empty($tok_a['token_type'])) {
			_exit_text('Invalid Access Token [AOB#045]', 400);
		}

		// Using the access token, we may look up details about the
		// resource owner.
		try {

			$x = $p->getResourceOwner($tok);
			$x = $x->toArray();
			$x['scope'] = explode(',', $x['scope']);

			$_SESSION['AppUser'] = $x['Contact'];
			$_SESSION['Company'] = $x['Company'];

			$_SESSION['uid'] = $x['Contact']['id'];
			$_SESSION['gid'] = $x['Company']['id'];
			$_SESSION['email'] = $x['Contact']['username'];

			$r = $_GET['r'];
			if (empty($r)) {
				$r = '/dashboard';
			}

			return $RES->withRedirect($r);

		} catch (\Exception $e) {
			Session::flash('fail', $e->getMessage());
		}

		return $RES->withRedirect($r);

	}

}
