<?php
/**
 * Dashboard Module
 */

namespace App\Module;

class Dashboard extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Dashboard');
		$a->get('/ajax', 'App\Controller\Dashboard\Ajax');
	}
}
