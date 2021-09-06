<?php
/**
 *
 */

namespace App\Controller\Settings;

class External extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings :: External' ],
		];

		return $RES->write( $this->render('settings/external.php', $data) );
	}
}
