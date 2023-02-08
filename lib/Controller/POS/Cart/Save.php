<?php
/**
 * Save a Cart
 *
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * Saves the Posted Data as a HOLD in the database, Triggers Printing
 * @see https://github.com/minciue/cloudprint/pull/6
 * @see http://stackoverflow.com/questions/18523826/printer-settings-with-google-cloud-print
 */

namespace OpenTHC\POS\Controller\POS\Cart;

use Edoceo\Radix\Session;

class Save extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'save':

			$dbc = $this->_container->DB;

			$idx_item = 0;
			foreach ($_POST as $k => $v) {
				if (preg_match('/^qty\-(\w{26})$/', $k, $m)) {
					$idx_item++;
				}
			}
			if ($idx_item == 0) {
				return $RES->withRedirect('/pos');
			}

			$sql = 'INSERT INTO b2c_sale_hold (contact_id, meta) VALUES (:c0, :m1) RETURNING id';
			$arg = [
				':c0' => $_SESSION['Contact']['id'],
				// ':t1' => 'general',
				':m1' => json_encode($_POST),
			];
			$hid = $dbc->fetchOne($sql, $arg);
			if (empty($hid)) {
				Session::flash('fail', 'Failed to place hold');
				return $RES->withRedirect('/pos');
			}
			Session::flash('info', sprintf('Hold #%d Confirmed', $hid));

			if ($auto_print_ticket) {
				// @todo Fire & Forget and HTTP Print Request to ... ?
				// HTTP::post('/api/print', array('object' => 'hold', 'object-id' => $hid));
			}

			return $RES->withRedirect('/pos');

		}

		Session::flash('fail', 'Invalid Input [PCS-055]');
		return $RES->withRedirect('/pos');

	}

}
