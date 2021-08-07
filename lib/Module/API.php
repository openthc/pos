<?php
/**
 * API Module
*/

namespace App\Module;

class API extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\API\Main');

		$a->post('/print', 'App\Controller\API\Print');

		$a->post('/sale', 'App\Controller\API\Sale');

	}

}
