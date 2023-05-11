<?php
/**
 * POS Receipt Handler
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Checkout;

use OpenTHC\POS\License;
use OpenTHC\POS\Sale;

class Receipt extends \OpenTHC\Controller\Base
{
	/**
	 * Get the Receipt
	 */
	function __invoke($REQ, $RES, $ARG)
	{
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
				// [
				// 	'type' => 'rpi',
				// 	'link' => 'https://192.168.2.237/print-server.php',
				// 	'name' => 'Direct To Localnet Print Server',
				// ],
				// [
				// 	'type' => 'air',
				// 	'name' => 'Air Print - BETA',
				// 	'link' => '',
				// ],
				// [
				// 	'name' => 'Application Direct',
				// 	'type' => 'app-print-direct',
				// 	'link' => 'ipp://192.168.2.237:631/TSC100'
				// ],
			]
		);

		$action = $_POST['a'] ?: $_GET['a'];
		switch ($action) {
		case 'pdf':
		case 'print':
		case 'print-receipt':
		case 'send-print':
			return $this->print($RES);
		case 'print-direct-link':
			return $this->print_direct_link($RES);
		case 'send-blank':
			return $RES->withRedirect('/pos/open');
		case 'send-email':
			return $this->_send_email($RES, $data);
		case 'send-phone':
			return $this->_send_phone($RES);
		}

		switch ($this->Mode) {
		case 'print-select':
			//require_once(APP_ROOT . '/view/pos/print-select.php');
			return $RES->write( $this->render('pos/checkout/receipt-select.php', $data) );
		}

		$dbc = $this->_container->DB;

		$S = new \OpenTHC\POS\B2C\Sale($dbc, $_GET['s']);
		$Sm = json_decode($S['meta'], true);

		$data['cash_incoming'] = $Sm['_POST']['cash_incoming'];
		$data['cash_outgoing'] = $Sm['_POST']['cash_outgoing'];

		$Company = new \OpenTHC\Company($dbc, $_SESSION['Company']);

		$data['auto-print'] = $Company->getOption(sprintf('/%s/receipt/print-auto', $License['id']));
		$data['send-via-email'] = $Company->getOption(sprintf('/%s/receipt/email', $License['id']));
		$data['send-via-phone'] = $Company->getOption(sprintf('/%s/receipt/phone', $License['id']));

		return $RES->write( $this->render('pos/checkout/receipt.php', $data) );

	}

	/**
	 * Actually Print
	 */
	function print($RES)
	{
		$dbc = $this->_container->DB;

		$S = new \OpenTHC\POS\B2C\Sale($dbc, $_GET['s']);

		$b2c_item_list = [];
		$res = $S->getItems();
		foreach ($res as $i => $b2ci) {

			$I = new \OpenTHC\POS\Lot($dbc, $b2ci['inventory_id']);
			$P = $dbc->fetchRow('SELECT id, name FROM product WHERE id = :p0', [ ':p0' => $I['product_id'] ]);
			$V = $dbc->fetchRow('SELECT id, name FROM variety WHERE id = :v0', [ ':v0' => $I['variety_id'] ]);

			$b2ci['Inventory'] = $I;
			$b2ci['Product'] = $P;
			$b2ci['Variety'] = $V;

			$b2c_item_list[] = $b2ci;
		}

		$pdf = new \OpenTHC\POS\PDF\Receipt();
		$pdf->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company'] ));
		$pdf->setLicense( new \OpenTHC\Company($dbc, $_SESSION['License'] ));
		$pdf->setSale($S);
		$pdf->setItems($b2c_item_list);
		$pdf->render();
		$name = sprintf('Receipt_%s.pdf', $S['id']);
		$pdf->Output($name, 'I');

		exit(0);
	}

	/**
	 *
	 */
	function print_direct_link($RES)
	{
		// Like Print Above, but Save PDF
		$pdf_file = sprintf('%s/webroot/output/%s.pdf', APP_ROOT, $ulid);
		$pdf_base = basename($pdf_file);

		return $RES->withJSON([
			'data' => sprintf('https://%s/output/%s.pdf', $_SERVER['SERVER_NAME'], $pdf_base),
			'meta' => [
				'expires_at' => 'THE_FUTURE',
			]
		]);

	}

	/**
	 * Send an Email of the Receipt
	 * @param [type] $RES [description]
	 * @param [type] $data [description]
	 * @return [type] [description]
	 */
	function _send_email($RES, $data)
	{
		$dbc = $this->_container->DB;
		$cfg = $dbc->fetchOne("SELECT val FROM auth_company_option WHERE key = 'pos-email-send'");
		if (empty($cfg)) {
			_exit_html_fail('<h1>Email Service is not configured [PCR-155]</h1>', 501);
		}
		$cfg = \json_decode($cfg, true);

		$_POST['receipt-email'] = trim(strtolower($_POST['receipt-email']));
		if (empty($_POST['receipt-email'])) {
			__exit_text('Invalid Email', 400);
		}

		/*
		$rcpt = $_POST['receipt-email'];
		$chk = \Edoceo\Radix\Net\HTTP::get('http://isvaliduser.com/api/check?e=' . \rawurlencode($rcpt));
		if (($chk['info']['http_code'] >= 200) && ($chk['info']['http_code'] <= 299)) {
			// OK
			$res = json_decode($chk['body'], true);
			$rcpt = $res['email'];
		} else {
			_exit_html_fail('<p>Invalid Email, <a href="/auth/open">try again</a>.</p>', 400);
		}
		*/

		$T = new \OpenTHC\POS\B2C\Sale($_GET['s']);

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
			_exit_html_fail('<h1>SMS Service is not configured [PCR-246]</h1>', 501);
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

		case 'openthc':

			$ghc = new \GuzzleHttp\Client([
				'base_uri' => 'https://openthc.pub',
				'headers' => [
					'user-agent' => 'OpenTHC/420.20.040',
					'authorization' => sprintf('Bearer %s', $this->_api_auth),
				],
				'http_errors' => false
			]);
			$arg = [
				'address' => $_POST['receipt-phone'],
				'message' => sprintf('https://%s/pub/receipt?_=%s', $_SERVER['SERVER_NAME'], $hash),
			];
			$res = $ghc->post('/api/send', $arg);
			$raw = $res->getBody()->getContents();
			$ret = json_decode($raw, true);
			if (empty($ret['code'])) {
				$ret['code'] = $res->getStatusCode();
			}

			break;

		}

		return $RES->withRedirect('/pos/checkout/done');

	}

}
