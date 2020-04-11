<?php
/**
 * POS Fast
*/

namespace App\Controller\POS;

use Edoceo\Radix\DB\SQL;

class Fast extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'POS :: Orders &amp; Delivery' ],
		];
		return $this->_container->view->render($RES, 'page/pos/fast.html', $data);
	}
}
