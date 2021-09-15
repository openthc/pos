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
		$data['b2c_sale_hold'] = [];

		// Select
		$dbc = $this->_container->DB;
		$data['b2c_sale_hold'] = $dbc->fetchAll('SELECT * FROM b2c_sale_hold');

		return $RES->write( $this->render('pos/online.php', $data) );
	}
}
