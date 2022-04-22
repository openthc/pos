<?php
/**
 * AJAX View Helper
 *
 * SPDX-License-Identifier: GPL-3.0-only
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

		ob_start();
		require_once(APP_ROOT . '/view/_block/inventory-list.php');
		$html = ob_get_clean();

		// return $RES->write( $this->render('_block/inventory-list.php', $data) );
		__exit_html($html);

	}
}
