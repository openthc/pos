<?php
/**
	CRM Home
*/

namespace App\Controller\CRM;

class Contact extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Contact List'),
		);

		return $this->_container->view->render($RES, 'page/crm/contact.html', $data);

	}

}
