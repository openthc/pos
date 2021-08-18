<?php
/**
 *
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
}
