<?php
/**
 * Creates a Fake Inbound Manifest
 */

namespace App\Controller\B2B\Incoming;

class Create extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		$data['Page'] = [ 'title' => 'Create Incoming' ];
		$data['License_Source'] = [];
		$data['License_Target'] = $_SESSION['License'];

		return $RES->write( $this->render('b2b/incoming/create.php', $data) );
	}

	function post($REQ, $RES, $ARG)
	{
		// Action Handler
		switch ($_POST['a']) {
			case 'save':

				$_POST['license'] = preg_replace('/[^\d]+/', null, $_POST['license']);
				$_POST['source-license'] = preg_replace('/[^\d]+/', null, $_POST['source-license']);
				$_POST['manifest-guid'] = preg_replace('/[^\d]+/', null, $_POST['manifest-guid']);

				$inventory_item_list = array();
				foreach ($_POST as $k => $v) {

					if (preg_match('/^inventory\-guid\-(.+)$/', $k, $m)) {

						$code = $m[1];

						$item = array(
							'barcodeid' => $_POST[sprintf('inventory-guid-%s', $code)],
							'invtype' => $_POST[sprintf('item-%s', $code)],
							'strain' => $_POST[sprintf('strain-name-%s', $code)],
							'productname' => $_POST[sprintf('product-name-%s', $code)],
							'quantity' => $_POST[sprintf('inventory-quantity-%s', $code)],
							'usableweight' => $_POST[sprintf('inventory-weight-%s', $code)],
							'price' => floatval($_POST[sprintf('inventory-price-%s', $code)])
						);

						$item['barcodeid'] = preg_replace('/[^\d]+/', null, $item['barcodeid']);
						if (empty($item['product'])) {
							$item['productname'] = 'X';
						}

						$inventory_item_list[] = $item;

					}

				}

				// $cre = App::cre();
				// $cre->b2b->incoming->create($arg);

			}

	}

}
