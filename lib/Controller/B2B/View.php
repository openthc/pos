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
		// if ('success' != $res['status']) {
		// 	// print_r($res);
		// 	die("Cannot Load Transfer");
		// }
		// _exit_text($data);

		$data = array(
			'Page' => array('title' => sprintf('Transfer %s', $ARG['id'])),
			'Transfer' => $res['result'],
		);

		return $this->_container->view->render($RES, 'page/b2b/view.html', $data);

	}
}
