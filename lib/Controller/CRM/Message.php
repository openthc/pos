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

	function email($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging :: Compose Email'),
		);

		return $this->_container->view->render($RES, 'page/crm/message-compose-email.html', $data);

	}

	function sms($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging :: Compose SMS'),
		);

		return $this->_container->view->render($RES, 'page/crm/message-compose-sms.html', $data);

	}


}
