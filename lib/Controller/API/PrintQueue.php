<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\API;

class PrintQueue extends \OpenTHC\POS\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$auth = $_SERVER['HTTP_AUTHORIZATION'];
		if ( ! preg_match('/^Bearer v2018\/print-queue\/([\w\-]+)$/', $auth, $m)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Authorization [CAP-018]' ],
			), 403);
		}

		$pq_code = $m[1];

		$rdb = $this->_container->Redis;
		$key0 = sprintf('/global/print-queue/%s', $pq_code);

		$key_list = $rdb->hkeys($key0);

		// $Contact = $this->findContact($dbc_auth, $act->contact);

		$Company = $rdb->hget($key0, 'Company');
		$Company = json_decode($Company, true);

		$License = $rdb->hget($key0, 'License');
		$License = json_decode($License, true);

		foreach ($key_list as $key1) {
			if ( ! preg_match('/^0\w{25}$/', $key1)) {
				continue;
			}

			$job = $rdb->hget($key0, $key1);
			if ( ! empty($job)) {
				$job = json_decode($job);
				switch ($job->type) {
					case 'pick-ticket':

						$rdb->hdel($key0, $key1);

						$pdf = new \OpenTHC\POS\PDF\PickTicket();
						$pdf->setCompany( new \OpenTHC\Company(null, $Company ));
						$pdf->setLicense( new \OpenTHC\License(null, $License ));
						$pdf->setData($job->data);
						$pdf->render();
						$name = sprintf('PickTicket_%s.pdf', $job->data->id);
						$pdf->Output($name, 'I');

						exit;

						break;

					case 'receipt':

						// Needs Data from Database for this
						// $b2c = new \Sale($dbc, $id);

						$pdf = new \OpenTHC\POS\PDF\Receipt();
						$pdf->setCompany( new \OpenTHC\Company(null, $Company ));
						$pdf->setLicense( new \OpenTHC\License(null, $License ));
						// $pdf->setSale($S);
						// $pdf->setItems($b2c_item_list);
						$pdf->render();
						$name = sprintf('Receipt_%s.pdf', $S['id']);
						$pdf->Output($name, 'I');

						exit;

						break;
				}
			}

		}

		return $RES->withJSON([
			'data' => $pq_data,
			'meta' => [ 'note' => 'No Print Jobs Found' ]
		], 404);

	}
}
