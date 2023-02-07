<?php
/**
 * Dashboard Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Module;

class Dashboard extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Dashboard\Main');
	}
}
