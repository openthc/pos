<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * API Module
 */

namespace App\Module;

class API extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\API\Main');

		// $a->post('/print', 'App\Controller\API\Print');

		// Create Sale
		$a->post('/b2c', 'App\Controller\API\B2C');

		$a->get('/b2c/receipt/preview', 'App\Controller\API\B2C\Receipt:preview');

		$a->get('/b2c/{id}', 'App\Controller\API\B2C\Single');

		$a->post('/b2c/{id}', 'App\Controller\API\B2C\Single:post');
		$a->post('/b2c/{id}/item', 'App\Controller\API\B2C\Item');

		$a->post('/b2c/{id}/verify', 'App\Controller\API\B2C\Single:verify');
		// $a->post('/b2c/{id}/payment', 'App\Controller\API\B2C\Single:post');
		$a->post('/b2c/{id}/commit', 'App\Controller\API\B2C\Single:commit');

	}

}
