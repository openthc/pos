<?php
/**
 * Dashboard Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class Dashboard extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\Dashboard\Main');
	}
}
