<?php
/**
 * A Inventory
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS;

class Inventory extends \OpenTHC\SQL\Record
{
	protected $_table = 'inventory';

	function decrement($x=1)
	{
		$sql = 'UPDATE inventory SET qty = qty - :d WHERE id = :id AND qty >= :d';
		$arg = array(
			':id' => $this->_data['id'],
			':d' => $x
		);
		$res = $this->_dbc->query($sql, $arg);
		if (empty($res)) {
			throw new \Exception("Could not Decrement Inventory");
		}
	}

}
