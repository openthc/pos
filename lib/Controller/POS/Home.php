<?php
/**
 * POS Home
*/

namespace App\Controller\POS;

use Edoceo\Radix\DB\SQL;

class Home extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		if (empty($_SESSION['pos-terminal-id'])) {
			$_SESSION['pos-terminal-id'] = \uniqid();
		}

		$data = array(
			'Page' => array('title' => 'POS :: #' . $_SESSION['pos-terminal-id']),
		);

		// Splice in Holds
		$chk = $this->_container->DB->fetchAll('SELECT count(id) FROM sale_hold');
		if ($chk) {
			$menu = $this->_container->view['menu'];
			$idx = 0;
			foreach ($menu['main'] as $i => $m) {
				if ('/pos' == $m['link']) {
					$idx = $i + 1;
					break;
				}
			}
			$x = array_splice($menu['main'], $idx, 0, array(array(
				'link' => '#',
				'html' => '<span data-toggle="modal" data-target="#pos-modal-sale-hold-list"><i class="fas fa-tags"></i> Holds</span>',
			)));
			$this->_container->view['menu'] = $menu;
		}

		if ('open' == $_GET['a']) {
			if (!empty($_GET['t'])) {

				$data['cart_item_list'] = array();

				$Cart = $this->_container->DB->fetchRow('SELECT * FROM sale_hold WHERE id = ?', array($_GET['t']));
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

		//var_dump($data);

		return $this->_container->view->render($RES, 'page/pos/home.html', $data);

	}

}
