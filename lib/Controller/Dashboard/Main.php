<?php
/**
 *
 * SPDX-License-Identifier: GPL-3.0-only
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
			'Page' => array('title' => 'Dashboard'),
		);

		$dbc_user = $this->_container->DB;
		$license_list = $dbc_user->fetchAll('SELECT * FROM license WHERE flag & :f1 = :f1', [
			':f1' => \OpenTHC\License::FLAG_MINE,
		]);

		foreach ($license_list as $l) {
			if ('retail' == $l['type']) {
				$_SESSION['License'] = $l;
				break;
			}
		}

		// if (empty($_SESSION['License'])) {
		// 	return $RES->write( $this->render('pick-license.php', $data) );
		// }

		return $RES->write( $this->render('dashboard.php', $data) );

	}

}
