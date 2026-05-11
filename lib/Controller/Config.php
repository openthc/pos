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
		$data = [];
		$data['Page'] = [];
		$data['Page']['title'] = 'Configuration';

		$rdb = $this->_container->Redis;

		$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$data['receipt-queue-id'] = $rdb->get($key);

		$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
		$data['pick-ticket-queue-id'] = $rdb->get($key);

		return $RES->write( $this->render('config.php', $data) );
	}

	function post($REQ, $RES, $ARG)
	{
		// __exit_text($_POST);
		switch ($_POST['a']) {
			case 'print-queue-receipt-update':
				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/receipt-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$val = $_POST['print-queue-code'];
				$rdb->set($key, $val);
				break;
			case 'print-queue-pick-ticket-update':
				$rdb = $this->_container->Redis;
				$key = sprintf('/%s/%s/pos/pick-ticket-queue', $_SESSION['Company']['id'], $_SESSION['License']['id']);
				$val = $_POST['print-queue-code'];
				$rdb->set($key, $val);
				break;

		}

		return $RES->withRedirect('/config');
	}

	function send_print_queue_script()
	{
		if ( ! empty($_GET['a'])) {
			if ('dlps' == $_GET['a']) {

				$pq_link = 'https://pos.openthc.dev/api/v2018/print/queue';
				$pq_code = 'UzPeENJDm30oLS1PoW0t3fsu2maX90lglBmZZStEoHU';
				$pq_name = 'Star TSP100 Cutter (TSP143)';

				$out_code = file_get_contents(APP_ROOT . '/bin/print-queue-poller.ps1');

				$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_URL}}', $pq_link, $out_code);
				$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_API_KEY}}', $pq_code, $out_code);
				$out_code = str_replace('{{OPENTHC_PRINT_QUEUE_PRINTER_NAME}}', $pq_name, $out_code);

				__exit_text($out_code);

			}
		}

		$_SESSION['printer']['pick-ticket'] = 'UzPeENJDm30oLS1PoW0t3fsu2maX90lglBmZZStEoHU';
	}

}
