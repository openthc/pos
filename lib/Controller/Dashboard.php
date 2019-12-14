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

		$_SESSION['License'] = array(
			'id' => 3358,
			'name' => 'WT Retail',
			'address_full' => '1752 NW Market St #955, Seattle, WA 98107'
		);

		return $this->_container->view->render($RES, 'page/dashboard.html', $data);

	}

}
