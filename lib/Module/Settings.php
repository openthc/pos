<?php
/**
 * Setting Module
 */

namespace App\Module;

class Settings extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Settings\Main');

		$a->get('/delivery', 'App\Controller\Settings\Delivery');
		$a->get('/external', 'App\Controller\Settings\External');

		$a->get('/printer', 'App\Controller\Settings\Printer');

		$a->get('/receipt', 'App\Controller\Settings\Receipt');

		$a->get('/receipt/preview', function() {

			$dbc = _dbc($_SESSION['dsn']);
			// $S = new \App\B2C\Sale($dbc, $_GET['s']);
			// $b2c_item_list = $S->getItems();
			// foreach ($b2c_item_list as $i => $b2ci) {
			// 	$b2c_item_list[$i]['Inventory'] = new \App\Lot($dbc, $b2ci['inventory_id']);
			// }

			$b2c = [
				'id' => 'PREVIEW',
				'created_at' => '1969-04-20T16:20:00 America/Los_Angeles',
			];

			$b2c_item_list = [
				'Inventory' => [],
				'Lot' => [],
			];

			$pdf = new \App\PDF\Receipt();
			$pdf->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company'] ));
			$pdf->setLicense( new \OpenTHC\Company($dbc, $_SESSION['License'] ));
			$pdf->setSale($b2c);
			$pdf->setItems($b2c_item_list);
			$pdf->render();
			$name = sprintf('receipt_%s.pdf', $S['id']);
			$pdf->Output($name, 'I');

			exit(0);

		});

		// // Reports
		// $this->group('/report', 'App\Module\Report');

	}
}
