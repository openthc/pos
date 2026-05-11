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
		$rdb = $this->_container->Redis;

		$source_data = $this->parseJSON();


		$pq_code = $_SESSION['printer']['pick-ticket'];
		if (empty($pq_code)) {
			__exit_json([
				'data' => 'Invalid Pick Ticket Printer Selected',
			], 400);
		}

		$key0 = sprintf('/global/print-queue/%s', $pq_code);
		$key1 = _ulid();

		$val = json_encode([
			'type' => 'pick-ticket',
			'data' => $source_data,
		]);

		$res = $rdb->hset($key0, $key1, $val);

		__exit_json([
			'data' => $res,
			'meta' => [],
		], 201);

	}
}
