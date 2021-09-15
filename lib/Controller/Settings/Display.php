<?php
/**
 *
 */

namespace App\Controller\Settings;

class Display extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings :: Display' ],
		];

		return $RES->write( $this->render('settings/display.php', $data) );
	}
}
