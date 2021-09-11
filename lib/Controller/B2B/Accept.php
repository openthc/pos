<?php
/**
 * Accept an Incoming Transfer
 */

namespace App\Controller\B2B;

class Accept extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			return $this->accept($RES, $ARG);
		}

		$dbc = $this->_container->DB;

		$arg = array($_SESSION['License']['id'], $ARG['id']);
		$T0 = $dbc->fetchRow('SELECT * FROM b2b_incoming WHERE license_id_target = ? AND id = ?', $arg);

		$cre = new \OpenTHC\RCE($_SESSION['pipe-token']);

		// Fresh data from CRE
		$res = $cre->get('/transfer/incoming/' . $ARG['id']);
		if ('success' != $res['status']) {
			_exit_html_fail('<h1>Cannot Load B2B Sale [CTA-028]</h1>', 500);
		}
		$T1 = $res['result'];

		$res = $cre->get('/config/section');
		if ('success' != $res['status']) {
			_exit_html_fail('<h1>Cannot load Section list from CRE [CTA-033]</h1>', 501);
		}
		$section_list = $res['result'];

		$Origin = $dbc->fetchRow('SELECT * FROM license WHERE guid = ?', array($T1['global_from_mme_id']));
		$Target = $dbc->fetchRow('SELECT * FROM license WHERE guid = ?', array($T1['global_to_mme_id']));

		$data = array(
			'Page' => array('title' => 'Transfer :: Accept'),
			'Transfer' => $T1,
			'Origin_License' => $Origin,
			'Target_License' => $Target,
			'Section_list' => $section_list,
		);

		return $RES->write( $this->render('b2b/accept.php', $data) );

	}

	/**
		Actually Accept the Inventory
	*/
	private function accept($RES, $ARG)
	{
		$cre = new \OpenTHC\RCE($_SESSION['pipe-token']);

		$args = array(
			'global_id' => $ARG['id'],
			'inventory_transfer_items' => array(),
		);

		foreach ($_POST as $k => $v) {

			if (preg_match('/lot-receive-guid-(\w+)/', $k, $m)) {

				$id = $m[1];
				$rx = floatval($_POST[sprintf('lot-receive-count-%s', $id)]);

				$iti = array(
					'global_id' => $v,
					'received_qty' => $rx,
					'global_received_area_id' => $_POST['section-id'],
				);

				$args['inventory_transfer_items'][] = $iti;
			}
		}

		$path = sprintf('/transfer/incoming/%s/accept', $ARG['id']);
		$res = $cre->post($path, array('json' => $args));

		if ('success' != $res['status']) {
			_exit_text($res);
		}


		// Add Lots to my Inventory
		$lot_list = $res['result']['inventory_transfer_items'];
		foreach ($lot_list as $lot) {
			// $dbc->insert('lot', array(
			// 	'id' => $lot['global_received_inventory_id'],
			// 	'company_id' => $_SESSION['company']['id'],
			// 	'license_id' => $_SESSION['license']['id'],
			// 	'name' => $lot['description'],
			// 	'meta' => \json_encode($lot),
			// ));
		}

		return $RES->withRedirect('/b2b/' . $ARG['id']);

	}
}
