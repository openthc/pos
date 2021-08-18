<?php
/**
 * POS Online
 */

namespace App\Controller\POS;

class Online extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'POS :: Online' ],
		];
		return $RES->write( $this->render('pos/online.php', $data) );
	}
}
