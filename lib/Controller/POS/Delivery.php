<?php
/**
 * POS Delivery
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS;

class Delivery extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'POS :: Delivery' ],
			'b2c_sale_hold' => [],
			'contact_list' => [], // Employee Contacts
			'courier_list' => [], // Active Courier Contacts
		];

		$dbc = $this->_container->DB;

		// Select Couriers
		$sql = "SELECT id, fullname AS name FROM contact";
		$data['contact_list'] = $dbc->fetchAll($sql);

		// Contacts are insert/delete from this table when active-on-shift
		$sql = "SELECT * FROM b2c_courier";
		// $res = $dbc->fetchAll($sql);
		$data['courier_list'] = [
			[
				'id' => 'ABC',
				'name' => 'David Busby'
				, 'stat' => 200
				, 'ping' => 240
				, 'location' => '53rd & 3rd'
			],
			[
				'id' => 'DEF',
				'name' => 'Bavid Dusby'
				, 'stat' => 200
				, 'ping' => 420
				, 'location' => '22 Acacia Avenue'
			]
		];


		// Select Orders
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

	/**
	 *
	 */
	function ajax($REQ, $RES, $ARG)
	{
		switch ($_GET['a']) {
			case 'delivery-auth':
				$link = sprintf('https://%s/intent?%s'
					, $_SERVER['SERVER_NAME']
					, http_build_query([
						'a' => 'delivery-auth',
						'c' => $_SESSION['Company']['id'],
						'l' => $_SESSION['License']['id']
					])
				);
				__exit_json([
					'data' => $link
				]);

		}

		__exit_json([
			'data' => null,
			'meta' => [ 'detail' => 'Error' ]
		], 400);

	}
}
