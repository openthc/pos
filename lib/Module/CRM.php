<?php
/**
 * CRM Module
 */

namespace App\Module;

class CRM extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\CRM\Main');
		$a->get('/contact', 'App\Controller\CRM\Contact');
		$a->post('/contact', 'App\Controller\CRM\Contact:save');
		$a->get('/message', 'App\Controller\CRM\Message');
		$a->get('/message/sms', 'App\Controller\CRM\Message:sms');
		$a->get('/message/email', 'App\Controller\CRM\Message:email');
		$a->get('/ajax', 'App\Controller\CRM\Ajax');
	}

}
