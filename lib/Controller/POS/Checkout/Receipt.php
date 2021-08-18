<?php
/**
 * POS Receipt Handler
*/

namespace App\Controller\POS\Checkout;

use App\License;
use App\Sale;

class Receipt extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		// $L = new License($this->_container->DB, $_SESSION['License']['id']);

		$data = array(
			'Page' => array('title' => 'POS :: Checkout :: Receipt'),
			'Company' => $_SESSION['Company'],
			'Sale' => array(
				'id' => $_GET['s'],
			),
			'cart_item_list' => array(),
			'printer_list' => [
				[
					'name' => 'Browser PDF',
					'type' => 'pdf',
				],
				// [
				// 	'type' => 'lpd',
				// 	'link' => 'https://localhost:8000/print-server.php',
				// 	'name' => 'Direct Localhost',
				// ],
				[
					'type' => 'rpi',
					'link' => 'https://192.168.2.237/print-server.php',
					'name' => 'Direct To Localnet Print Server',
				],
				// [
				// 	'type' => 'air',
				// 	'name' => 'Air Print - BETA',
				// 	'link' => '',
				// ],
			]
		);

		switch ($_POST['a']) {
		case 'pdf':
		case 'print':
		case 'print-receipt':
		case 'send-print':
			return $this->print($RES);
		case 'send-blank':
			return $RES->withRedirect('/pos');
		case 'send-email':
			$_POST['receipt-email'] = trim(strtolower($_POST['receipt-email']));
			if (empty($_POST['receipt-email'])) {
				__exit_text('Invalid Email', 400);
			}
			return $this->_send_email($RES, $data);
		case 'send-phone':
			return $this->_send_phone($RES);
		}

		switch ($this->Mode) {
		case 'print-select':
			//require_once(APP_ROOT . '/view/pos/print-select.php');
			return $RES->write( $this->render('pos/checkout/receipt-select.php', $data) );
		}

		return $RES->write( $this->render('pos/checkout/receipt.php', $data) );

	}

	/**
	 * Actually Print
	 */
	function print($RES)
	{
		$dbc = $this->_container->DB;

		$S = new \App\B2C\Sale($dbc, $_GET['s']);

		$b2c_item_list = $S->getItems();
		foreach ($b2c_item_list as $i => $b2ci) {
			$b2c_item_list[$i]['Inventory'] = new \App\Lot($dbc, $b2ci['inventory_id']);
		}

		$pdf = new \App\PDF\Receipt();
		$pdf->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company'] ));
		$pdf->setLicense( new \OpenTHC\Company($dbc, $_SESSION['License'] ));
		$pdf->setItems($b2c_item_list);
		$pdf->render();
		$name = sprintf('receipt_%s.pdf', $S['id']);
		$pdf->Output($name, 'I');

		exit(0);
	}

	/**
	 * Send an Email of the Receipt
	 * @param [type] $RES [description]
	 * @param [type] $data [description]
	 * @return [type] [description]
	 */
	function _send_email($RES, $data)
	{
		$cfg = SQL::fetch_one("SELECT val FROM auth_company_option WHERE key = 'pos-email-send'");
		if (empty($cfg)) {
			_exit_fail('<h1>Email Service is not configured</h1>', 501);
		}
		$cfg = \json_decode($cfg, true);

		$rcpt = $_POST['receipt-email'];
		$chk = \Edoceo\Radix\Net\HTTP::get('http://isvaliduser.com/api/check?e=' . \rawurlencode($rcpt));
		if (($chk['info']['http_code'] >= 200) && ($chk['info']['http_code'] <= 299)) {
			// OK
			$res = json_decode($chk['body'], true);
			$rcpt = $res['email'];
		} else {
			exit;
			_exit_fail('<p>Invalid Email, <a href="/auth/open">try again</a>.</p>', 400);
		}

		$T = new \App\B2C\Sale($_GET['s']);

		$data['cart_item_list'] = $T->getItems();

		$subj = 'Receipt from ' . $_SESSION['Company']['name'];


		$sub_total = 99.99;
		$tax_total = $sub_total * 0.101;
		$ext_total = $sub_total * 0.350;
		$all_total = number_format($sub_total + $tax_total + $ext_total, 2);

		$body = <<<EOB
A receipt for your items purchased from {$_SESSION['Company']['name']}

{% for ci in cart_item_list %}
	Item: {{ ci.name }}
	Cost: {{ ci.unit_price }}

{% endfor %}

Sub-Total:                                               {$sub_total}
Sales Tax:                                               {$tax_total}
Excise Tax:                                              {$ext_total}
                                                         ------------
                                                         {$all_total}
---
Thank you for shopping at {$_SESSION['Company']['name']}
EOB;

		$hash = md5($rcpt.$subj.$body);

		$mail = <<<EOM
From: "{$_SESSION['Company']['name']}" <null@openthc.com>
To: <$rcpt>
Subject: $subj
Message-Id: <$hash@openthc.com>
MIME-Version: 1.0
Content-Type: text/plain; boundary="$hash"; charset="utf-8"

$body
EOM;

		$smtp = new \Net_SMTP($cfg['host'], $cfg['port'], $cfg['helo']);
		$smtp->setDebug(true);
		$smtp->connect();
		$smtp->helo($cfg['helo']);
		$smtp->mailFrom($cfg['from']);
		$smtp->rcptTo($rcpt);
		$smtp->data($mail);
		$smtp->disconnect();

		return $RES->withRedirect('/pos/checkout/done');

	}

	/**
	 *
	 */
	function _send_phone($RES)
	{
		$dbc = $this->_container->DB;

		$hash = _encrypt(json_encode(array(
			'c' => $_SESSION['Company']['id'],
			's' => $_GET['s'],
		)));

		$cfg = $dbc->fetchOne("SELECT val FROM auth_company_option WHERE key = 'pos-phone-send'");
		if (empty($cfg)) {
			_exit_fail('<h1>SMS Service is not configured</h1><p>Please <a href="/settings/receipt">update the settings</a></p>', 501);
		}
		$cfg = \json_decode($cfg, true);

		$cfg['engine'] = 'rcpt.fyi';
		$cfg['bearer'] = '31aaff1c0d406d4a13ecc366a7f8f2e4e7f5b33b17ec05dafdb0bf5c3b95c418';

		switch ($cfg['engine']) {
		case 'plivo':
			$PRC = new \Plivo\RestClient($cfg['sid'], $cfg['key']);
			$PRC->messages->create($_POST['receipt-phone'], array(
				'from' => $cfg['cid'],
				'body' => sprintf('Receipt #%d at https://%s/pub/receipt?_=%s', $_GET['s'], $_SERVER['SERVER_NAME'], $hash)
			));
			break;
		case 'twilio':
			$TRC = new \Twilio\Rest\Client($cfg['sid'], $cfg['key']);
			$TRC->messages->create($_POST['receipt-phone'], array(
				'from' => $cfg['cid'],
				'body' => sprintf('Receipt #%d at https://%s/pub/receipt?_=%s', $_GET['s'], $_SERVER['SERVER_NAME'], $hash)
			));
			break;
		case 'rcpt.fyi':
			$ghc = new \GuzzleHttp\Client([
				'base_uri' => 'https://rcpt.fyi/send',
				'headers' => [
					'user-agent' => 'OpenTHC/420.20.040',
					'authorization' => sprintf('Bearer %s', $this->_api_auth),
				],
				'http_errors' => false
			]);
			$arg = [
				'to' => $_POST['receipt-phone'],
				'document' => sprintf('https://%s/pub/receipt?_=%s', $_SERVER['SERVER_NAME'], $hash),
			];
			$res = $ghc->post($url, $arg);
			$raw = $res->getBody()->getContents();
			$ret = json_decode($raw, true);
			if (empty($ret['code'])) {
				$ret['code'] = $res->getStatusCode();
			}

			exit;


		}

		return $RES->withRedirect('/pos/checkout/done');

	}

	function _load_email_from_twig($file, $data)
	{
		$tlf = new \Twig_Loader_Filesystem(sprintf('%s/twig/email', APP_ROOT));
		$cfg = array(
			'strict_variables' => true,
		);
		$twig = new \Twig_Environment($tlf, $cfg);
		$twig->addFilter(new \Twig_SimpleFilter('base64', function($x) {
			return chunk_split(base64_encode($x), 72);
		}));

		$base = array(
			'app_url' => \OpenTHC\Config::get('application.base'),
			'mail_hash' => sha1(openssl_random_pseudo_bytes(512)),
		);

		$data = array_merge($base, $data);

		$mail = $twig->render($file, $data);

		return $mail;
	}

}
