<?php
/**
 * POS Online
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS;

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
		$sql = <<<SQL
SELECT b2c_sale_hold.*
 , contact.fullname AS contact_name
FROM b2c_sale_hold
LEFT JOIN contact ON b2c_sale_hold.contact_id = contact.id
WHERE b2c_sale_hold.type IN ('online')
SQL;

		$data['b2c_sale_hold'] = $dbc->fetchAll("SELECT * FROM b2c_sale_hold WHERE type = 'online'");

		return $RES->write( $this->render('pos/online.php', $data) );
	}
}
