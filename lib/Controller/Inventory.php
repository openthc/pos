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

		return $this->_container->view->render($RES, 'page/inventory/home.html', $data);

	}

}
