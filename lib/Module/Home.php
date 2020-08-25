<?php
/**
 * Home Module
 */

namespace App\Module;

class Home extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Home');
		$a->get('/ajax', 'App\Controller\Home\Ajax');
	}
}
