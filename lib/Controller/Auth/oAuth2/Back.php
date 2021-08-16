<?php
/**
 * oAuth2 Returns Here
 */

namespace App\Controller\Auth\oAuth2;

class Back extends \OpenTHC\Controller\Auth\oAuth2
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$p = $this->getProvider();

		if (empty($_GET['code'])) {
			__exit_text('Invalid Link [AOB-018]', 400);
		}

		// Check State
		$this->checkState();

		$tok = null;

		// Try to get an access token using the authorization code grant.
		try {
			$tok = $p->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
		} catch (\Exception $e) {
			__exit_text('Invalid Access Token [AOB-027]', 400);
		}

		if (empty($tok)) {
			__exit_text('Invalid Access Token [AOB-031]', 400);
		}

		$chk = json_decode(json_encode($tok), true);
		if (empty($chk['access_token'])) {
			__exit_text('Invalid Access Token [AOB-036]', 400);
		}
		if (empty($chk['scope'])) {
			__exit_text('Invalid Access Token [AOB-039]', 400);
		}
		if (empty($chk['token_type'])) {
			__exit_text('Invalid Access Token [AOB-042]', 400);
		}
		if ('bearer' != $chk['token_type']) {
			__exit_text('Invalid Access Token [AOB-045]', 400);
		}

		// Using the access token, we may look up details about the
		// resource owner.
		try {

			$x = $p->getResourceOwner($tok)->toArray();

			$_SESSION['Contact'] = $x['Contact'];
			$_SESSION['Company'] = $x['Company'];
			$_SESSION['email'] = $x['Contact']['username'];

			return $RES->withRedirect('/auth/init?' . http_build_query([
				'r' => $_GET['r']
			]));

		} catch (\Exception $e) {
			__exit_text($e->getMessage() . ' [AOB-071]', 500);
		}

		__exit_text('Invalid Request [AOB-072]', 400);
	}

}
