<?php
/**
 * Save a Cart
 * Saves the Posted Data as a HOLD in the database, Triggers Printing
 * @see https://github.com/minciue/cloudprint/pull/6
 * @see http://stackoverflow.com/questions/18523826/printer-settings-with-google-cloud-print
 */

namespace App\Controller\POS\Cart;

use Edoceo\Radix;
use Edoceo\Radix\Session;
use Edoceo\Radix\DB\SQL;

class Save extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'save':

			$dbc = $this->_container->DB;

			$idx_item = 0;
			foreach ($_POST as $k => $v) {
				if (preg_match('/^qty\-(\d+)$/', $k, $m)) {
					$idx_item++;
				}
			}
			if ($idx_item == 0) {
				die("No Hold");
				Radix::redirect('/pos');
			}

			// $_SESSION['uid']
			$sql = 'INSERT INTO b2c_sale_hold (contact_id, meta) VALUES (?, ?) RETURNING id';
			$arg = array(2, json_encode($_POST));
			$hid = $dbc->fetchOne($sql, $arg);
			if (empty($hid)) {
				Session::flash('fail', 'Failed to place hold');
				Radix::redirect();
			}
			Session::flash('info', sprintf('Hold #%d Confirmed', $hid));

			if ($auto_print_ticket) {
				// @todo Fire & Forget and HTTP Print Request to ... ?
				// HTTP::post('/api/print', array('object' => 'hold', 'object-id' => $hid));
			}

			Radix::redirect();

		}

		Session::flash('fail', 'CPS#022: Invalid Input');
		Radix::redirect();

	}
}
