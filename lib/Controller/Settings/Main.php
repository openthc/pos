<?php
/**
 *
 */

namespace App\Controller\Settings;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings' ]
		];

		return $RES->write( $this->render('settings/main.php', $data) );
	}
}
