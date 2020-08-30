<?php
/**
 * A Retail Sale
*/

namespace App\B2C;

class Sale extends \OpenTHC\SQL\Record
{
	protected $_table = 'b2c_sale';

	private $_item_list;

	public static function import($x)
	{
		$License = \OpenTHC\License::findByGUID($x['location']);

		$gri = sprintf('%s://%016d', $_SESSION['cre'], $x['transactionid']);

		$sql = 'SELECT * FROM b2c_sale WHERE id = ?';
		$arg = array($gri);
		$res = $this->_dbc->fetchRow($sql, $arg);
		if (empty($res)) {
			$S = new Sale();
			$S['id'] = $id;
			$S['uid'] = $_SESSION['Contact']['id'];
			$S['license_id'] = $License['id'];
			$S->save();
		} else {
			$S = new Sale($res);
		}

		return $S;

	}

	function addItem($I, $q)
	{
		$this->_item_list[] = array(
			'id' => $I['id'],
			'size' => $q,
		);

	}

	function getItems()
	{
		$sql = 'SELECT * FROM b2c_sale_item WHERE b2c_sale_id = ? ORDER BY sort, id';
		$arg = array($this->_data['id']);
		$res = $this->_dbc->fetchAll($sql, $arg);
		return $res;
	}

}
