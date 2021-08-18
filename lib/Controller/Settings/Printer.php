<?php
/**
 *
 */

namespace App\Controller\Settings;

class Printer extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings :: Printer' ],
		];

		return $RES->write( $this->render('settings/printer.php', $data) );
	}
}
