<?php
/**
 * CRM Main
 */

namespace App\Controller\CRM;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM'),
		);

		return $RES->write( $this->render('crm/main.php', $data) );

	}

}