<?php
/**
 * Checkout Done
 */

namespace App\Controller\POS\Checkout;

class Done extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'POS :: Checkout :: Done')
		);

		return $this->_container->view->render($RES, 'page/pos/checkout/done.html', $data);
	}
}
