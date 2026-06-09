<?php
/**
 * Set POS Terminal Options
 *
 * SPDX-License-Identifier: MIT
 */

namespace OpenTHC\POS\Controller;

class Config extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		if ( ! empty($_GET['a'])) {
			switch ($_GET['a']) {
				case 'download-script':
					$this->send_print_queue_script();
					break;
			}
			__exit_text('Invalid Request [LCC-020]', 400);
		}
		$data = [];
		$data['Page'] = [];
		$data['Page']['title'] = 'POS / Configuration';

		// Database Print Queue
		$sql = <<<SQL
		SELECT *
		FROM auth_company_option
		WHERE key LIKE 'print-queue%'
		SQL;

		$dbc = $this->_container->DB;
		$res = $dbc->fetchAll($sql);
		$data['print_queue_list'] = []; //$res_print_queue;
		foreach ($res as $rec) {
			$pq0 = json_decode($rec['val']);
			$pq0->id = str_replace('print-queue-', '', $rec['key']);
			$pq0->key = $rec['key'];
			$data['print_queue_list'][] = $pq0;
		}

		$rdb = $this->_container->Redis;

		$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$val = $rdb->get($key);
		$val = json_decode($val, true);
		$data['receipt-queue-id'] = $val['queue-id'];
		$data['receipt-printer-name'] = $val['printer-name'];

		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$val = $rdb->get($key);
		$val = json_decode($val, true);
		$data['pick-ticket-queue-id'] = $val['queue-id'];
		$data['pick-ticket-printer-name'] = $val['printer-name'];

		return $RES->write( $this->render('config/main.php', $data) );
	}

	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
			case 'print-queue-receipt-update':

				$qid = $_POST['print-queue-id'];

				$dbc = $this->_container->DB;

				$pq1 = [];
				$res = $dbc->fetchRow('SELECT * FROM auth_company_option WHERE key = :k0', [
					':k0' => sprintf('print-queue-%s', $qid)
				]);
				$res['val'] = json_decode($res['val'], true);
				$pq1['id'] = $qid;
				$pq1['queue-id'] = $qid;
				$pq1['device-name'] = $res['val']['printer-name'];
				$pq1['printer-name'] = $res['val']['printer-name'];

				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$rdb->del($key);
				$val = json_encode($pq1);
				$rdb->set($key, $val);

				break;

			case 'print-queue-pick-ticket-update':

				$qid = $_POST['print-queue-id'];

				$dbc = $this->_container->DB;

				$pq1 = [];
				$res = $dbc->fetchRow('SELECT * FROM auth_company_option WHERE key = :k0', [
					':k0' => sprintf('print-queue-%s', $qid)
				]);
				$res['val'] = json_decode($res['val'], true);
				$pq1['id'] = $qid;
				$pq1['queue-id'] = $qid;
				$pq1['device-name'] = $res['val']['printer-name'];
				$pq1['printer-name'] = $res['val']['printer-name'];

				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$rdb->del($key);
				$val = json_encode($pq1);
				$rdb->set($key, $val);

				break;

		}

		return $RES->withRedirect('/config');
	}

	function send_print_queue_script()
	{
		$rdb = $this->_container->Redis;

		$pq_type = $_GET['s'];
		switch ($pq_type) {
			case 'pick-ticket':
				break;
			case 'receipt':
				break;
			default:
				__exit_text('Invalid Request [LCC-130]', 400);
		}


		$key = sprintf('/%s/%s/pos/%s-queue', $_SESSION['Company']['id'], $_SESSION['License']['id'], $pq_type);
		$val = $rdb->get($key);
		$val = json_decode($val, true);

		$pq_link = sprintf('%s/api/v2018/print/queue', \OpenTHC\Config::get('openthc/pos/origin'));
		$pq_code = $val['queue-id'];
		$pq_name = $val['printer-name'];

		$out_code = file_get_contents(APP_ROOT . '/bin/print-queue-poller.ps1');

		$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_URL}}', $pq_link, $out_code);
		$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_API_KEY}}', $pq_code, $out_code);
		$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_PRINTER_NAME}}', $pq_name, $out_code);

		header('cache-control: no-cache');
		// header('content-type: text/plain');
		// header('content-type: application/x-powershell');
		header('content-type: application/octet-stream');
		header(sprintf('content-disposition: attachment; filename="openthc-print-queue-%s.ps1"', $pq_type));

		echo $out_code;

		exit(0);

	}

}
