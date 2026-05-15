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

		// Accept: text/html,application/xhtml+xml,application/xml;q=0.9,application/json;q=0.8,*/*;q=0.5
		$want_type = $_SERVER['HTTP_ACCEPT'];
		$want_type = explode(',', $want_type);
		switch ($want_type[0]) {
			case 'application/pdf':

				$pdf = new \OpenTHC\POS\PDF\PickTicket();
				$pdf->setCompany( new \OpenTHC\Company(null, $_SESSION['Company'] ));
				$pdf->setLicense( new \OpenTHC\License(null, $_SESSION['License'] ));
				$pdf->setData((object)$source_data);
				$pdf->render();
				$name = sprintf('PickTicket_%s.pdf', 'TEST');
				$pdf->Output($name, 'I');
				exit;

				break;
		}
		// $want_type = $REQ->wantType();

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
