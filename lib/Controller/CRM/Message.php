<?php
/**

*/

namespace App\Controller\CRM;

class Message extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging'),
		);

		return $this->_container->view->render($RES, 'page/crm/message.html', $data);

	}

}
