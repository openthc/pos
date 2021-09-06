<?php
/**
 *
 */

namespace App\Controller\Settings;

class Delivery extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => [ 'title' => 'Settings :: Delivery' ],
		];

		return $RES->write( $this->render('settings/delivery.php', $data) );
	}
}
