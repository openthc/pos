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
		return $this->_container->view->render($RES, 'page/pos/delivery.html', $data);
	}
}
