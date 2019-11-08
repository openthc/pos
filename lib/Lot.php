<?php
/**
 * A Lot
 */

namespace App;

use Edoceo\Radix\DB\SQL;

class Lot extends \OpenTHC\SQL\Record
{
	protected $_table = 'inventory';

	function decrement($x=1)
	{
		$sql = 'UPDATE inventory SET qty = qty - :d WHERE id = :id AND qty >= :d';
		$arg = array(
			':id' => $this->_data['id'],
			':d' => $x
		);
		$res = SQL::query($sql, $arg);
		if (empty($res)) {
			throw new Exception("Could not Decrement Inventory");
		}
	}

}
