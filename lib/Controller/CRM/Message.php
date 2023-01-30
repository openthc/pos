<?php
/**
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\CRM;

class Message extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging'),
		);

		return $RES->write( $this->render('crm/message.php', $data) );

	}

	function email($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging :: Compose Email'),
		);

		return $RES->write( $this->render('crm/message-compose-email.php', $data) );

	}

	function sms($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Messaging :: Compose SMS'),
		);

		return $RES->write( $this->render('crm/message-compose-sms.php', $data) );

	}


}
