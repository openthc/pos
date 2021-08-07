<?php
/**
	Module for Online and Onsite Menus
*/

namespace App\Module;

class Menu extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Menu\Main');
		$a->get('/online', 'App\Controller\Menu\Online');
		$a->get('/onsite', 'App\Controller\Menu\Onsite');
	}

}
