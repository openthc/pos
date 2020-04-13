<?php
/**
 * POS Online
 */

namespace App\Controller\POS;

use Edoceo\Radix\DB\SQL;

class Online extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'POS :: Online' ],
		];
		return $this->_container->view->render($RES, 'page/pos/online.html', $data);
	}
}
