<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * API Module
 */

namespace OpenTHC\POS\Module;

class API extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\API\Main');

		// $a->post('/print', 'OpenTHC\POS\Controller\API\Print');

		// Create Sale
		$a->post('/b2c', 'OpenTHC\POS\Controller\API\B2C');

		$a->get('/b2c/receipt/preview', 'OpenTHC\POS\Controller\API\B2C\Receipt:preview');

		$a->get('/b2c/{id}', 'OpenTHC\POS\Controller\API\B2C\Single');

		$a->post('/b2c/{id}', 'OpenTHC\POS\Controller\API\B2C\Single:post');
		$a->post('/b2c/{id}/item', 'OpenTHC\POS\Controller\API\B2C\Item');

		$a->post('/b2c/{id}/verify', 'OpenTHC\POS\Controller\API\B2C\Single:verify');
		// $a->post('/b2c/{id}/payment', 'OpenTHC\POS\Controller\API\B2C\Single:post');
		$a->post('/b2c/{id}/commit', 'OpenTHC\POS\Controller\API\B2C\Single:commit');

	}

}
