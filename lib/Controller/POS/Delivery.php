<?php
/**
 * POS Delivery
 */

namespace App\Controller\POS;

class Delivery extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'POS :: Delivery' ],
		];
		$data['map_api_key_js'] = \OpenTHC\Config::get('google/map_api_key_js');

		return $RES->write( $this->render('pos/delivery.php', $data) );
	}
}
