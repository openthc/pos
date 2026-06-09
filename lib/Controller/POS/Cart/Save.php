<?php
/**
 * Save a Cart
 *
 * SPDX-License-Identifier: MIT
 *
 * Saves the Posted Data as a HOLD in the database, Triggers Printing
 * @see https://github.com/minciue/cloudprint/pull/6
 * @see http://stackoverflow.com/questions/18523826/printer-settings-with-google-cloud-print
 */

namespace OpenTHC\POS\Controller\POS\Cart;

use Edoceo\Radix\Session;

class Save extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'save':

			$dbc = $this->_container->DB;

			$Cart = [];
			$Cart['id'] = '';
			$Cart['name'] = $_POST['name'];
			$Cart['name'] = trim($Cart['name']);
			$Cart['item_list'] = [];

			$idx_item = 0;
			foreach ($_POST as $k => $v) {
				if (preg_match('/^item\-(\w{26})\-unit\-count$/', $k, $m)) {
					$idx_item++;
					$Cart['item_list'][] = [
						// 'id' => $m[1],
						'inventory_id' => $m[1],
						'unit_count' => $v,
						// unit_price and all that?
					];
				}
			}
			if ($idx_item == 0) {
				return $RES->withRedirect('/pos');
			}

			$sql = 'INSERT INTO b2c_sale_hold (contact_id, meta) VALUES (:c0, :m1) RETURNING id';
			$arg = [
				':c0' => $_SESSION['Contact']['id'],
				// ':t1' => 'general',
				':m1' => json_encode($Cart),
			];
			$hid = $dbc->fetchOne($sql, $arg);
			if (empty($hid)) {
				Session::flash('fail', 'Failed to place hold');
				return $RES->withRedirect('/pos');
			}
			Session::flash('info', sprintf('Hold #%d Confirmed', $hid));

			if ($auto_print_ticket = false) {
				// @todo Fire & Forget and HTTP Print Request to ... ?
				// HTTP::post('/api/print', array('object' => 'hold', 'object-id' => $hid));
			}

			return $RES->withRedirect('/pos');

		}

		Session::flash('fail', 'Invalid Input [PCS-055]');
		return $RES->withRedirect('/pos');

	}

}
