<?php
/**
 *
 */

namespace App\Controller;

class Dashboard extends \OpenTHC\Controller\Base
{
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
		// 	return $this->_container->view->render($RES, 'page/pick-license.html', $data);
		// }

		// return $RES->write( $this->render($file, $data) );

		return $this->_container->view->render($RES, 'page/dashboard.html', $data);

	}

}
