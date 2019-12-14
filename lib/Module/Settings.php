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

		// $this->get('', function($REQ, $RES, $ARG) {
		// 	$data = array();
		// 	$this->view->render($RES, 'page/settings/index.html', $data);
		// });
	
		// // Reports
		// $this->group('/report', 'App\Module\Report');
				
	}
}
