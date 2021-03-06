<?php
/**
 * Search and Import B2B
 */

namespace App\Controller\B2B;

class Home extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		// Load Transfer Data
		$sql = 'SELECT * FROM b2b_incoming WHERE license_id_target = :l ORDER BY created_at DESC';
		$arg = array(':l' => $_SESSION['License']['id']);
		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			$rec['meta'] = json_decode($rec['meta'], true);
			$rec['date'] = strftime('%m/%d', strtotime($rec['meta']['created_at']));
			$rec['origin_license'] = $dbc->fetchRow('SELECT * FROM license WHERE id = ?', array($rec['license_id_source']));
			$rec['target_license'] = $dbc->fetchRow('SELECT * FROM license WHERE id = ?', array($rec['license_id_target']));
			$transfer_list[] = $rec;
		}

		$data = array(
			'Page' => array('title' => 'Transfers'),
			'transfer_list' => $transfer_list,
		);

		return $this->_container->view->render($RES, 'page/b2b/index.html', $data);

	}

}
