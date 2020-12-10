<?php
/**
 * Inventory Module
 */

namespace App\Module;

class Inventory extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Inventory');

		$a->get('/create', 'App\Controller\Inventory\Create');
		$a->post('/create', 'App\Controller\Inventory\Create:post');

		$a->get('/view', 'App\Controller\Inventory\View');
		// $a->get('/samples', 'App\Controller\Inventory\Samples');
		$a->get('/samples', function($REQ, $RES, $ARG) {
			return $this->view->render($RES, 'page/inventory/sample-list.html', []);
		});
		$a->map(['GET','POST'], '/ajax', 'App\Controller\Inventory\Ajax');
	}
}
