<?php
/**
 * View a Single Inventory Item
 *
 * @todo To Detect if QA has Passed (for Sub-Lots) you have to look at TraceabilityData['inventoryparentid'] QA data
 * @todo the Parent may also have a QA Sample Sub-Lot var/sync/6/inventory/3587383023657792.json iwth Ivneory Status 2
 */

namespace App\Controller\Inventory;

class View extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$L = new \App\Lot($dbc, $_GET['id']);
		if (empty($L['id'])) {
			Session::flash('fail', 'No Inventory Specified');
			return(0);
		}
		$L['unit_price'] = $L['sell'];

		$P = new \App\Product($dbc, $L['product_id']);
		$PT = new \App\Product\Type($dbc, $P['product_type_id']);
		$V = new \App\Variety($dbc, $L['variety_id']);
		$S = new \App\Section($dbc, $L['section_id']);

		$data = [];
		$data['Page'] = [ 'title' => 'Inventory :: View '];

		$data['Lot'] = $L;
		$data['Product'] = $P;
		$data['Product_Type'] = $PT;
		$data['Variety'] = $V;
		$data['Section'] = $S;

		return $RES->write( $this->render('inventory/view.php', $data) );
	}
}
