<?php
/**
 * Module for Online and Onsite Menus
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class Menu extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\Menu\Main');
		$a->get('/online', 'OpenTHC\POS\Controller\Menu\Online');
		$a->get('/onsite', 'OpenTHC\POS\Controller\Menu\Onsite');
	}

}
