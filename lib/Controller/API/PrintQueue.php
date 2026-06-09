<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 *
 * SPDX-License-Identifier: MIT
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

		// $Contact = $this->findContact($dbc_auth, $act->contact);

		$Company = $rdb->hget($key0, 'Company');
		$Company = json_decode($Company, true);

		$License = $rdb->hget($key0, 'License');
		$License = json_decode($License, true);

		$job_list = $rdb->hkeys($key0);
		foreach ($job_list as $key1) {

			// Filter for PrintJob looking keys
			if ( ! preg_match('/^0\w{25}$/', $key1)) {
				continue;
			}

			$job = $rdb->hget($key0, $key1);
			if ( ! empty($job)) {
				$job = json_decode($job);
				switch ($job->type) {
					case 'pick-ticket': // @deprecated

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

					case 'pick-ticket-pdf':
					case 'receipt':

						$rdb->hdel($key0, $key1);

						// It's a PDF Blob in Redis
						$pdf_data = base64_decode($job->data);

						header('cache-control: no-store, private'); // , post-check=0, pre-check=0, max-age=1');
						// header('cache-control: must-revalidate, no-cache, no-store, private'); // , post-check=0, pre-check=0, max-age=1');
						//header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1

						header('content-disposition: inline; filename="%s"', rawurlencode(basename($job->name)));
						// header('Content-Disposition: inline; filename="' . rawurlencode(basename($name)) . '"; ' .
								// 'filename*=UTF-8\'\'' . rawurlencode(basename($name)));
						header('content-length: %d', strlen($pdf_data));
						header('content-type: application/pdf');

						// Force Download
						// header('Content-Description: File Transfer');
						// Sets a Bunch of Headers?
						// header('Content-Type: application/force-download');
						// header('Content-Type: application/octet-stream', false);
						// header('Content-Type: application/download', false);
						// header('Content-Type: application/pdf', false);
						// header('Content-Disposition: attachment; filename="' . rawurlencode(basename($name)) . '"; ' .
						// 		'filename*=UTF-8\'\'' . rawurlencode(basename($name)));
						// header('Content-Transfer-Encoding: binary');

						echo $pdf_data;

						exit;

						break;
				}
			}

		}

		return $RES->withJSON([
			'data' => date('Y-m-d H:i:s'),
			'meta' => [ 'note' => 'No Print Jobs Found' ]
		], 404);

	}
}
