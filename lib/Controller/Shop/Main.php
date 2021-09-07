<?php
/**
 *
 */

namespace App\Controller\Shop;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = $this->data;
		$html = $this->render('shop/main.php', $data);
		return $RES->write($html);
	}
}
