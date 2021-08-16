<?php
/**
 * View a Single Inventory Item
 *
 * @todo To Detect if QA has Passed (for Sub-Lots) you have to look at TraceabilityData['inventoryparentid'] QA data
 * @todo the Parent may also have a QA Sample Sub-Lot var/sync/6/inventory/3587383023657792.json iwth Ivneory Status 2
 */

namespace App\Controller\Inventory;

class Create extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{

		$data = [];
		$data['Page'] = [ 'title' => 'Inventory :: Create' ];
		$data['License'] = $_SESSION['License'];

		return $RES->write( $this->render('inventory/create.php', $data) );

	}

	function post($REQ, $RES, $ARG)
	{

	}

}
