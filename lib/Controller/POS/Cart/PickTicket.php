<?php
/**
 * Submit a Cart to Pick Ticket
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Cart;

class PickTicket extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$source_data = $this->parseJSON();

		$rdb = $this->_container->Redis;
		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$val = $rdb->get($key);
		$val = json_decode($val, true);

		if (empty($val)) {
			__exit_json([
				'data' => 'Invalid Pick Ticket Printer Selected',
			], 400);
		}

		$pq_code = $val['queue-id'];

		$key0 = sprintf('/global/print-queue/%s', $pq_code);
		$key1 = _ulid();

		$val = json_encode([
			'type' => 'pick-ticket',
			'data' => $source_data,
		]);

		$res = $rdb->hset($key0, $key1, $val);

		__exit_json([
			'data' => $val,
			'meta' => [],
		], 201);

	}
}
