<?php
/**
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Settings;

class Receipt extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [
			'Page' => ['title' => 'Settings :: Receipt' ],
		];

		$dbc = _dbc($_SESSION['dsn']);

		$pdfX = new \App\PDF\Receipt();
		$pdfX->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company']) );
		$data['receipt-head'] = $pdfX->loadHeadText();
		$data['receipt-tail'] = $pdfX->loadTailText();
		$data['receipt-foot'] = $pdfX->loadFootText();


		return $RES->write( $this->render('settings/receipt.php', $data) );
	}

	/**
	 *
	 */
	function preview($REQ, $RES, $ARG)
	{

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

		$b2c_item_list = [];
		for ($idx=0; $idx<10; $idx++) {
			$b2c_item_list[] = [
				'Inventory' => [
					'guid' => _ulid()
				],
				'Lot' => [],
				'Product' => [
					'name' => 'Text/Product'
				],
				'Variety' => [
					'name' => 'Text/Variety',
				],
				'unit_count' => rand(1, 50),
				'unit_price' => rand(200, 20000) / 100
			];
		}

		$pdf = new \App\PDF\Receipt();
		$pdf->setCompany( new \OpenTHC\Company($dbc, $_SESSION['Company'] ));
		$pdf->setLicense( new \OpenTHC\Company($dbc, $_SESSION['License'] ));
		$pdf->setSale($b2c);
		$pdf->setItems($b2c_item_list);
		$pdf->render();
		$name = sprintf('receipt_%s.pdf', $S['id']);
		$pdf->Output($name, 'I');

		exit(0);

	}
}
