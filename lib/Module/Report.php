<?php
/**
 * POS Module
*/

namespace App\Module;

class Report extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Report\Home');
		$a->get('/b2c/recent', function($REQ, $RES, $ARG) {
			return $this->view->render($RES, 'page/report/b2c/recent.html', []);
		});
		$a->map(['GET', 'POST'], '/ajax', 'App\Controller\Report\Ajax');
		$a->get('/ajax/revenue-daily', function($REQ, $RES, $ARG) {
			require_once(APP_ROOT . '/lib/Controller/Report/Ajax.php');
		});
		$a->get('/ajax/revenue-product-type', function($REQ, $RES, $ARG) {
			require_once(APP_ROOT . '/lib/Controller/Report/revenue-product-type.php');
		});
	}

}
