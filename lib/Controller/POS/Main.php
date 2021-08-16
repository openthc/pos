<?php
/**
 * POS Main
*/

namespace App\Controller\POS;

use Edoceo\Radix\Session;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$sql = 'SELECT count(id) FROM lot_full WHERE license_id = :l0 AND stat = 200 AND qty > 0 AND sell IS NOT NULL and sell > 0';
		$arg = [
			':l0' => $_SESSION['License']['id']
		];
		$chk = $dbc->fetchOne($sql, $arg);
		if (empty($chk)) {
			__exit_text('Inventory Lots need to be present and priced for the POS to operate [CPH-020]', 400);
		}


		if (empty($_SESSION['pos-terminal-id'])) {
			$_SESSION['pos-terminal-id'] = _ulid();
		}

		// if ('auth' == $_GET['v']) {
		if (empty($_SESSION['pos-terminal-contact'])) {
			$data = [];
			$data['Page'] = [ 'title' => 'Terminal Authentication'];
			return $RES->write( $this->render('pos/open.php', $data) );
		}

		if ('scan' == $_GET['v']) {
			$data = [];
			$data['Page'] = [ 'title' => 'ID Scanner'];
			return $RES->write( $this->render('pos/scan-id.php', $data) );
		}

		$data = array(
			'Page' => array('title' => 'POS :: #' . $_SESSION['pos-terminal-id']),
		);

		// Splice in Holds
		$chk = $dbc->fetchAll('SELECT count(id) FROM b2c_sale_hold');
		if ($chk) {
			// Get Current Meny
			$menu = $this->_container->view['menu'];
			// Find Index of the Spot I want
			$idx = 0;
			foreach ($menu['main'] as $i => $m) {
				if ('/pos' == $m['link']) {
					$idx = $i + 1;
					break;
				}
			}
			// Splice my HOLDS item in there
			$x = array_splice($menu['main'], $idx, 0, array(array(
				'link' => '#',
				'html' => '<span data-toggle="modal" data-target="#pos-modal-sale-hold-list"><i class="fas fa-tags"></i> Holds</span>',
			)));
			// Update Data for Render
			$this->_container->view['menu'] = $menu;
		}

		if (('open' == $_GET['a']) && !empty($_GET['t'])) {

			$data['cart_item_list'] = array();

			$Cart = $this->_container->DB->fetchRow('SELECT * FROM b2c_sale_hold WHERE id = ?', array($_GET['t']));
			if (!empty($Cart['id'])) {

				$Cart['meta'] = json_decode($Cart['meta'], true);

				foreach ($Cart['meta'] as $k => $v) {
					if (preg_match('/^qty\-(\d+)$/', $k, $m)) {
						//$I = new \Inventory($m[1]);
						$I = $this->_container->DB->fetchRow('SELECT id, sell FROM inventory WHERE id = ?', array($m[1]));
						$data['cart_item_list'][] = array(
							'id' => $I['id'],
							'name' => $Cart['meta']['name'],
							'weight' => floatval($I['unit_weight']),
							'price' => floatval($I['sell']),
							'qty' => intval($v),
						);
					}
				}
			}
		}

		return $RES->write( $this->render('pos/terminal/main.php', $data) );

	}

	/**
	 * POST Handler
	 */
	function post($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'auth-code':

			// Lookup Contact by this Auth Code
			$code = $_POST['code'];

			// Assign to Register Session
			// Set Expiration in T minutes?
			$R = $this->_container->Redis;
			$k = sprintf('/%s/pos-terminal', $_SESSION['Contact']['id']);
			$v = $_SESSION['Contact']['id'];
			$R->set($k, $v, [ 'ttl' => 600 ]);

			$_SESSION['pos-terminal-contact'] = $_SESSION['Contact']['id'];

			return $RES->withRedirect('/pos');

			break;
		}

	}

	private function openCart()
	{
	}

}
