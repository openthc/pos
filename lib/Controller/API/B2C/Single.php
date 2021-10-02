<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\API\B2C;

class Single extends \App\Controller\API\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		if ( ! preg_match('/^01\w{24}$/', $ARG['id'])) {
			__exit_json([
				'data' => null
				, 'meta' => [ 'detail' => 'Invalid Request [ABS-020]' ]
			], 400);
		}

		$dbc = _dbc($this->Company['dsn']);

		// Fetch the Specified B2C Obbject
		$b2c = new \App\B2C\Sale($dbc, $ARG['id']);
		if (empty($b2c['id'])) {
			__exit_json([
				'data' => null
				, 'meta' => [ 'detail' => 'Not Found [ABS-029]' ]
			], 404);
		}

		$b2c_item_list = $b2c->getItems();

		// Format Output
		$ret = $b2c->toArray();
		$ret['license'] = [ 'id' => $ret['license_id'] ];
		unset($ret['license_id']);
		$ret['item_list'] = $b2c_item_list;

		__exit_text([
			'data' => $ret
			, 'meta' => []
		]);
	}

	/**
	 *
	 */
	function commit($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		// Check ID
		if (!preg_match('/^01\w{24}$/', $ARG['id'])) {
			return $this->failure($RES, 'Invalid Request [ABS-055]', 400);
		}

		$source_data = $this->parseJSON();
		if (empty($source_data['license']['id'])) {
			return $this->failure($RES, 'Invalid Request [ABS-060]', 400);
		}

		$dbc = _dbc($this->Company['dsn']);
		$dbc->query('BEGIN');

		// Fetch the Specified B2C Obbject
		$b2c = new \App\B2C\Sale($dbc, $ARG['id']);
		if (empty($b2c['id'])) {
			return $this->failure($RES, 'Not Found [ABS-063]', 404);
		}
		if (100 != $b2c['stat']) {
			return $this->failure($RES, 'Invalid B2C Sale State [ABS-072]', 404);
		}

		// Get Items and Decrement
		$sum_item_price = 0;
		$b2c_item_list = $b2c->getItems();
		foreach ($b2c_item_list as $b2c_item) {
			$IL = new \App\Lot($dbc, $b2c_item['inventory_id']);
			try {
				$IL->decrement($b2c_item['unit_count']);
			} catch (\Exception $e) {
				// Ignore
			}
			$sum_item_price += ($b2c_item['unit_count'] * $b2c_item['unit_price']);
		}
		$b2c['full_price'] = $sum_item_price;

		// Add Tax Line Items?
		$pr0 = new \App\Product($dbc);
		$pr0->loadBy('product_type_id', '018NY6XC00PT000000TAXSALES'); // Well Known Tax Product ID

		$b2c_item = new \App\B2C\Sale\Item($dbc);
		$b2c_item['b2c_sale_id'] = $b2c['id'];
		$b2c_item['inventory_id'] = '01FH12BJ7P47WKE8Q1SQMC5VF6'; // Well known Inventory ID to represent a Tax
		$b2c_item['unit_count'] = 1;
		$b2c_item['unit_price'] = $b2c['full_price'] * 0.1010;
		// $b2c_item->save();

		// Add 502 Tax Line Items
		$pr1 = new \App\Product($dbc);
		$pr1->loadBy('product_type_id', '018NY6XC00PT000000TAXOTHER'); // Well Known Tax Product ID

		$b2c_item = new \App\B2C\Sale\Item($dbc);
		$b2c_item['b2c_sale_id'] = $b2c['id'];
		$b2c_item['inventory_id'] = '01FH12CJVQ0BJZD3CDQNHTG6DZ'; // Well known Inventory ID to represent a Tax
		$b2c_item['unit_count'] = 1;
		$b2c_item['unit_price'] = $b2c['full_price'] * 0.3300;
		// $b2c_item->save();

		$b2c['stat'] = 200;
		$b2c->save('B2C/Sale/Commit via API');

		$dbc->query('COMMIT');

		// @todo send to the CRE, or just add to the pump?

		return $RES->withJSON([
			'data' => $b2c->toArray()
			, 'meta' => []
		]);

	}

	/**
	 * Verify the Transaction
	 */
	function verify($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		// Check ID
		if (!preg_match('/^01\w{24}$/', $ARG['id'])) {
			return $this->failure($RES, 'Invalid Request [ABS-073]', 400);
		}

		$dbc = _dbc($this->Company['dsn']);

		// Fetch the Specified B2C Obbject
		$b2c = new \App\B2C\Sale($dbc, $ARG['id']);
		if (empty($b2c['id'])) {
			return $this->failure($RES, 'Not Found [ABS-081]', 400);
		}

		// $b2c_item_list = $b2c->getItems();

		// Fetch Product Type Limits
		// $res_product_limit = $dbc->fetchAll('SELECT * FROM b2c_sale_product_limit WHERE ')


		// __exit_json([
		// 	'data' => null
		// 	, 'meta' => [ 'detail' => 'Not Implemented [ABS-063]' ]
		// ], 501);

		return $RES->withJSON([
			'data' => null
			, 'meta' => [ 'detail' => 'No Limits Apply' ]
		]);
	}

}
