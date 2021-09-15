<?php
/**
 *
 */

namespace App\Controller\Settings;

class Terminal extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings :: Terminal' ],
		];

		return $RES->write( $this->render('settings/terminal.php', $data) );
	}
}
