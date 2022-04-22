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
		$data = [];

		$html = $this->render('report/recent.php', $data);

		return $RES->write($html);

	}

}
