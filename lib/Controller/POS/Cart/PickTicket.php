<?php
/**
 * Submit a Cart to Pick Ticket
 *
 * SPDX-License-Identifier: MIT
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

		$dbc = _dbc($_SESSION['dsn']);

		$pdf = new \OpenTHC\POS\PDF\PickTicket();
		$pdf->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company'] ));
		$pdf->setLicense( new \OpenTHC\License($dbc, $_SESSION['License'] ));
		$pdf->setData((object)$source_data);
		$pdf->render();
		$pdf_name = sprintf('PickTicket_%s.pdf', 'TEST');

		// Accept: text/html,application/xhtml+xml,application/xml;q=0.9,application/json;q=0.8,*/*;q=0.5
		// $want_type = $REQ->wantType();
		$want_type = $_SERVER['HTTP_ACCEPT'];
		$want_type = explode(',', $want_type);
		switch ($want_type[0]) {
			case 'application/pdf':
				$pdf->Output($pdf_name, 'I');
				exit;
				break;
		}

		// Send Pick Ticket for This Item
		$rdb = $this->_container->Redis;
		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$val = $rdb->get($key);
		$val = json_decode($val, true);
		if (empty($val)) {
			__exit_json([
				'data' => 'Invalid Pick Ticket Printer Selected',
			], 400);
		}

		$qid = $val['queue-id'];
		$key0 = sprintf('/global/print-queue/%s', $qid);
		$key1 = _ulid();

		$pdf_data = $pdf->Output($pdf_name, 'S');

		$val = json_encode([
			'type' => 'pick-ticket-pdf',
			'name' => $name,
			'data' => base64_encode($pdf_data),
		]);

		$res = $rdb->hset($key0, $key1, $val);

		__exit_json([
			'data' => $val,
			'meta' => [],
		], 201);

	}
}
