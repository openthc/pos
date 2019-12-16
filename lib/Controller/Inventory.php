<?php
/**
 *
 */

namespace App\Controller;

class Inventory extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
        if (empty($_GET['view'])) {
            $_GET['view'] = '100';
        }

		$data = array(
            'Page' => array('title' => 'Inventory'),
            'view_mode' => $_GET['view'],
		);

		$_SESSION['License'] = array(
			'id' => 3358,
			'name' => 'WT Retail',
			'address_full' => '1752 NW Market St #955, Seattle, WA 98107'
		);

		return $this->_container->view->render($RES, 'page/inventory/home.html', $data);

	}

}
