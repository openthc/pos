<?php
/**
 * Set POS Terminal Options
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

		$rdb = $this->_container->Redis;

		$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$data['receipt-queue-id'] = $rdb->get($key);

		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$val = $rdb->get($key);
		$val = json_decode($val, true);
		$data['pick-ticket-queue-id'] = $val['queue-id'];
		$data['pick-ticket-printer-name'] = $val['printer-name'];

		return $RES->write( $this->render('config.php', $data) );
	}

	function post($REQ, $RES, $ARG)
	{
		// __exit_text($_POST);
		switch ($_POST['a']) {
			case 'print-queue-receipt-update':
				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$rdb->del($key);
				$val = json_encode([
					'queue-id' => $_POST['print-queue-code'],
					'printer-name' => $_POST['printer-name'],
				]);
				$rdb->set($key, $val);
				break;
			case 'print-queue-pick-ticket-update':
				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$rdb->del($key);
				$val = json_encode([
					'queue-id' => $_POST['print-queue-code'],
					'printer-name' => $_POST['printer-name'],
				]);
				$rdb->set($key, $val);
				break;

		}

		return $RES->withRedirect('/config');
	}

	function send_print_queue_script()
	{
		$rdb = $this->_container->Redis;

		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
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
		header('content-disposition: attachment; filename="openthc-print-queue.ps1"');

		echo $out_code;

		exit(0);

	}

}
