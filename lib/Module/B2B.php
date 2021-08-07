<?php
/**
 * Wraps all the Routing for the B2B Module
 */

namespace App\Module;

class B2B extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\B2B\Main');

		// $a->get('/incoming');
		$a->get('/incoming/create', 'App\Controller\B2B\Incoming\Create');
		$a->post('/incoming/create', 'App\Controller\B2B\Incoming\Create:post');

		$a->map(['GET','POST'], '/sync', 'App\Controller\B2B\Sync');
		// $a->map(['GET', 'POST'], '/{id}/sync', 'App\Controller\B2B\Sync');



		$a->get('/{id}', 'App\Controller\B2B\View');
		$a->post('/{id}', 'App\Controller\B2B\View');

		$a->get('/{id}/accept', 'App\Controller\B2B\Accept');
		$a->post('/{id}/accept', 'App\Controller\B2B\Accept');

	}
}
