<?php
/**
 * View a Single Transfer
 */

namespace App\Controller\B2B;

class View extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$cre = new \OpenTHC\CRE($_SESSION['pipe-token']);
		$res = $cre->get('/transfer/incoming/' . $ARG['id']);

		$data = array(
			'Page' => array('title' => sprintf('Transfer %s', $ARG['id'])),
			'Transfer' => $res['data'],
		);

		return $RES->write( $this->render('b2b/view.php', $data) );

	}
}
