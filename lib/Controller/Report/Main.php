<?php
/**
 * Basic Report Stuff
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Report;

use Edoceo\Radix\Session;

class Main extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];

		$html = $this->render('report/main.php', $data);

		return $RES->write($html);

	}

	/**
	 *
	 */
	function recent($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$sql = 'SELECT * FROM b2c_sale WHERE license_id = :l0 AND stat = :s0';
		$b2c_transactions = $dbc->fetchAll($sql, [
			':s0' => \App\B2C\Sale::STAT_OPEN,
			':l0' => $_SESSION['License']['id'],
		]);
		$data = [];
		$data['b2c_transactions'] = $b2c_transactions;

		$html = $this->render('report/recent.php', $data);

		return $RES->write($html);

	}

}
