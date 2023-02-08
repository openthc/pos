<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\API\B2C;

class Item extends \OpenTHC\POS\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		if ( ! preg_match('/^01\w{24}$/', $ARG['id'])) {
			return $this->failure($RES, 'Invalid Request [ABI-015]', 400);
		}

		$source_data = $this->parseJSON();

		if (empty($source_data['license']['id'])) {
			return $this->failure($RES, 'Invalid License ID [ABI-021]', 400);
		}
		if (empty($source_data['lot']['id'])) {
			return $this->failure($RES, 'Invalid Lot [ABI-024]', 400);
		}
		if (empty($source_data['unit_count'])) {
			return $this->failure($RES, 'Invalid Request [ABI-027]', 400);
		}


		$dbc = _dbc($this->Company['dsn']);

		// Load Sale
		$b2c = new \OpenTHC\POS\B2C\Sale($dbc, $ARG['id']);
		if (empty($b2c['id'])) {
			return $this->failure($RES, 'Invalid Sale ID [ABI-032]', 400);
		}

		// Check Lot
		$lot = new \OpenTHC\POS\Lot($dbc, $source_data['lot']['id']);
		if (empty($lot['id'])) {
			return $this->failure($RES, 'Invalid Lot [ABI-038]', 400);
		}

		if (empty($source_data['unit_price'])) {
			$source_data['unit_price'] = $lot['sell'];
		}

		if ($lot['qty'] < $source_data['unit_count']) {
			return $this->failure($RES, 'Invalid Quantity [ABI-046]', 400);
		}

		// Add Item
		$b2c_item = new \OpenTHC\POS\B2C\Sale\Item($dbc);
		$b2c_item['b2c_sale_id'] = $b2c['id'];
		$b2c_item['inventory_id'] = $source_data['lot']['id'];
		$b2c_item['unit_count'] = floatval($source_data['unit_count']);
		$b2c_item['unit_price'] = floatval($source_data['unit_price']);
		// $b2c_item['full_price'] = $b2c_item['unit_count'] * $b2c_item['unit_price'];
		$b2c_item->save();

		__exit_json([
			'data' => $b2c_item->toArray()
			, 'meta' => []
		]);

	}
}
