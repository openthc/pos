<?php
/**
 * Webhook Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

use Edoceo\Radix\ULID;

class Webhook extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\Webhook\Main');

		$a->post('/weedmaps/order', function($REQ, $RES, $ARG) {

			$file = sprintf('%s/var/weedmaps-order-%s.txt', APP_ROOT, ULID::create());
			$json = file_get_contents('php://input');
			file_put_contents($file, json_encode([
				'_GET' => $_GET,
				'_POST' => $_POST,
				'_ENV' => $_ENV,
				'_BODY' => $json
			]));

			$data = json_decode($json, true);
			switch ($data['status']) {
			case 'DRAFT':
				__exit_json($data);
				break;
			case 'PENDING':
				__exit_json($data);
				break;
			}

			__exit_json([
				'data' => null,
				'meta' => [ 'note' => 'Request Not Handled' ]
			], 400);

		});

	}
}
