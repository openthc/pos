<?php
/**
 *
 */

namespace App\Controller\Inventory;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		if (empty($_GET['view'])) {
			$_GET['view'] = '100';
		}

		$data = array(
			'Page' => array('title' => 'Inventory'),
			'view_mode' => $_GET['view'],
		);

		return $RES->write( $this->render('inventory/main.php', $data) );

	}

}
