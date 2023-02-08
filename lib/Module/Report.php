<?php
/**
 * Report Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class Report extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\Report\Main');
		$a->get('/b2c/recent', 'OpenTHC\POS\Controller\Report\Main:recent');
		// , function($REQ, $RES, $ARG) {
		// 	return $this->view->render($RES, 'page/report/b2c/recent.html', []);
		// });
		$a->map(['GET', 'POST'], '/ajax', 'OpenTHC\POS\Controller\Report\Ajax');
		$a->get('/ajax/revenue-daily', function($REQ, $RES, $ARG) {
			require_once(APP_ROOT . '/lib/Controller/Report/Ajax.php');
		});
		$a->get('/ajax/revenue-product-type', function($REQ, $RES, $ARG) {
			require_once(APP_ROOT . '/lib/Controller/Report/revenue-product-type.php');
		});
	}

}
