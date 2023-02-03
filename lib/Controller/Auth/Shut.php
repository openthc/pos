<?php
/**
 * Shut Authenticated Session
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\Auth;

class Shut extends \OpenTHC\Controller\Auth\Shut
{
	function __invoke($REQ, $RES, $ARG)
	{
		$res0 = parent::__invoke($REQ, $RES, $ARG);
		if (200 != $res0->getStatusCode()) {
			return $res0;
		}

		$data = [];
		$data['Page'] = [ 'title' => 'Session Closed' ];
		$data['body'] = '<p>Your session has been closed</p><p>';
		$data['foot'] = '<a class="btn btn-outline-secondary" href="/auth/open">Sign In Again</a>';

		return $RES->write( $this->render('done.php', $data) );

	}

}
