<?php
/**
 * Wraps all the Routing for the B2B Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class B2B extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\B2B\Main');

		// $a->get('/incoming');
		$a->get('/incoming/create', 'OpenTHC\POS\Controller\B2B\Incoming\Create');
		$a->post('/incoming/create', 'OpenTHC\POS\Controller\B2B\Incoming\Create:post');

		$a->map(['GET','POST'], '/sync', 'OpenTHC\POS\Controller\B2B\Sync');
		// $a->map(['GET', 'POST'], '/{id}/sync', 'OpenTHC\POS\Controller\B2B\Sync');



		$a->get('/{id}', 'OpenTHC\POS\Controller\B2B\View');
		$a->post('/{id}', 'OpenTHC\POS\Controller\B2B\View');

		$a->get('/{id}/accept', 'OpenTHC\POS\Controller\B2B\Accept');
		$a->post('/{id}/accept', 'OpenTHC\POS\Controller\B2B\Accept');

	}
}
