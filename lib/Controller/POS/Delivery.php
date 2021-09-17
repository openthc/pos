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
			'b2c_sale_hold' => [],
		];

		// Select
		$dbc = $this->_container->DB;
		$sql = <<<SQL
SELECT b2c_sale_hold.*
 , contact.fullname AS contact_name
FROM b2c_sale_hold
LEFT JOIN contact ON b2c_sale_hold.contact_id = contact.id
WHERE b2c_sale_hold.type IN ('delivery', 'general')
SQL;
		$data['b2c_sale_hold'] = $dbc->fetchAll($sql);

		$data['map_api_key_js'] = \OpenTHC\Config::get('google/map_api_key_js');

		return $RES->write( $this->render('pos/delivery.php', $data) );
	}
}
