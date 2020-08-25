<?php
/**
 *
 */

namespace App\Controller;

class Home extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'Home'),
		);

		return $this->_container->view->render($RES, 'page/home.html', $data);

	}

}
