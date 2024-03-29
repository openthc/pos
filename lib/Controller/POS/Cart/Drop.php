<?php
/**
 * Drop a Cart
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS\Cart;

class Drop extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_POST['a']) {
		case 'drop':

			$dbc = $this->_container->DB;

			$sql = 'DELETE FROM b2c_sale_hold WHERE id = ?';
			$arg = [ $_POST['cart'] ];
			$dbc->query($sql, $arg);

			return $RES->withJSON(null, 204);

		}
		_exit_json([
			'meta' => [ 'detail' => 'Invalid Input [PCD#023]' ],
		], 400);
	}
}
