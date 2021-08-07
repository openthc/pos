<?php
/**
 * CRM Main
 */

namespace App\Controller\CRM;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM'),
		);

		return $this->_container->view->render($RES, 'page/crm/main.html', $data);

	}

}
