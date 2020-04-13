<?php
/**
	CRM Home
*/

namespace App\Controller\CRM;

class Home extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM'),
		);

		return $this->_container->view->render($RES, 'page/crm/home.html', $data);

	}

}
