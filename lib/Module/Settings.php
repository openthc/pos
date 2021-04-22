<?php
/**
 * Setting Module
 */

namespace App\Module;

class Settings extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', function($REQ, $RES, $ARG) {
			$data = [
				'Page' => ['title' => 'Settings' ]
			];
			return $this->view->render($RES, 'page/settings.html', $data);
		});

		$a->get('/printer', function($REQ, $RES, $ARG) {
			$data = [
				'Page' => ['title' => 'Settings :: Printer' ],
			];
			return $this->view->render($RES, 'page/settings/printer.html', $data);
		});

		$a->get('/receipt', function($REQ, $RES, $ARG) {
			$data = [
				'Page' => ['title' => 'Settings :: Receipt' ],
			];
			return $this->view->render($RES, 'page/settings/receipt.html', $data);
		});

		$a->get('/receipt/preview', 'App\Controller\Settings\Receipt\Preview');

		// // Reports
		// $this->group('/report', 'App\Module\Report');

	}
}
