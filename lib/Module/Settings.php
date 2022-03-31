<?php
/**
 * Setting Module
 */

namespace App\Module;

class Settings extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Settings\Main');

		$a->get('/delivery', 'App\Controller\Settings\Delivery');
		$a->get('/display', 'App\Controller\Settings\Display');
		$a->get('/external', 'App\Controller\Settings\External');
		$a->get('/printer', 'App\Controller\Settings\Printer');
		$a->get('/receipt', 'App\Controller\Settings\Receipt');
		$a->get('/receipt/preview', 'App\Controller\Settings\Receipt:preview');
		$a->get('/terminal', 'App\Controller\Settings\Terminal');

		// // Reports
		// $this->group('/report', 'App\Module\Report');

	}
}
