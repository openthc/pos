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
		$L = new \App\Lot($_GET['id']);
		if (empty($L['id'])) {
			Session::flash('fail', 'No Inventory Specified');
			return(0);
		}
		$P = new \App\Product($L['product_id']);
		$PT = new \App\Product\Type($P['product_type_id']);

		$data = [];
		$data['Page'] = [ 'title' => 'Inventory :: View '];
		// $_ENV['h1'] = $_ENV['title'] = 'Inventory :: ' . UI_GUID::format($this->Inventory['guid']);
		// $_ENV['body-head-menu'] = MRU::get('Inventory');
		// MRU::add('Inventory', array(
		// 	'name' => sprintf('%s: %s', UI_GUID::format($this->Inventory['guid'], true), $this->Inventory['name_strain']),
		// 	'link' => sprintf('/inventory/view?id=%d', $this->Inventory['id']),
		// ));
		// $this->Batch = new Batch($this->Inventory['batch_id']);
		// $this->Strain = new Strain($this->Inventory['strain_id']);
		// $x = UI_Inventory::icon($this->Inventory);
		// if (!empty($x)) {
		// 	$_ENV['h1'] .= ' <small>' . $x . '</small>';
		// }

		// Inventory Photo
		// $img_file = sprintf('%s/webroot/img/inventory/%06d/%d.png', APP_ROOT, $_SESSION['gid'], $this->Inventory['id']);
		// $img_link = sprintf('/img/inventory/%06d/%d.png', $_SESSION['gid'], $this->Inventory['id']);


		$data['Lot'] = $L;
		$data['Product'] = $P;
		$data['Product_Type'] = $PT;

		return $this->_container->view->render($RES, 'page/inventory/view.html', $data);
	}
}
