<?php
/**
 * Inventory Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Module;

class Inventory extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->map(['GET','POST'], '/ajax', 'App\Controller\Inventory\Ajax');
		$a->get('/view', 'App\Controller\Inventory\View');
	}
}
