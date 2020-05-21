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

		return $this->_container->view->render($RES, 'page/dashboard.html', $data);

	}

}
