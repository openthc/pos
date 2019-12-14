<?php
/**
	CRM Module
*/

namespace App\Module;

class CRM extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\CRM\Home');
		$a->get('/contact', 'App\Controller\CRM\Contact');
		$a->get('/message', 'App\Controller\CRM\Message');
		$a->get('/ajax', 'App\Controller\CRM\Ajax');
	}

}
