<?php
/**
 *
 * SPDX-License-Identifier: MIT
 */

namespace OpenTHC\POS\Controller\Dashboard;

class Main extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'POS / Dashboard'),
		);

		if (empty($_SESSION['License']['id'])) {
			return $RES->withRedirect('/auth/pick/license');
		}

		return $RES->write( $this->render('dashboard.php', $data) );

	}

}
