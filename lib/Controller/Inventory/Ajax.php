<?php
/**
 *
 */

namespace App\Controller\Inventory;

class Ajax extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		$data['inventory_list'] = [];

		$sql = <<<SQL
SELECT *,
sell AS unit_price
FROM lot_full
WHERE license_id = :l0
  AND stat IN (1, 200)
  AND qty > 0
SQL;

		$arg = [ ':l0' => $_SESSION['License']['id'] ];

		$data['inventory_list'] = $this->_container->DB->fetchAll($sql, $arg);

		return $RES->write( $this->render('_block/inventory-list.php', $data) );
	}
}
