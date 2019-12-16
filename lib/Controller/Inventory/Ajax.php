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
SELECT inventory.*
, inventory.sell AS unit_price
, product.name AS product_name
, product.package_unit_qom
, product.package_unit_uom
, product_type.id AS product_type_id
, product_type.name AS product_type_name
, product_type.mode AS product_type_mode
, product_type.unit AS package_uom
FROM inventory
JOIN product ON product.id = inventory.product_id
JOIN product_type ON product.product_type_id = product_type.id
WHERE inventory.license_id = :l0 AND inventory.stat IN (1, 200) AND inventory.qty > 0
SQL;

		$arg = [ ':l0' => $_SESSION['License']['id'] ];

		$data['inventory_list'] = $this->_container->DB->fetchAll($sql, $arg);

		return $this->_container->view->render($RES, 'block/inventory-list.html', $data);
	}
}