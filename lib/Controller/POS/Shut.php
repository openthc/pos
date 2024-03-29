<?php
/**
 * Register/Terminal Shut
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller\POS;

use Edoceo\Radix\Session;

class Shut extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		unset($_SESSION['pos-terminal-id']);
		unset($_SESSION['pos-terminal-contact']);

		// $R = $this->_container->Redis;
		// $R->hset(sprintf('pos-terminal-%s', $_SESSION['pos-terminal-id']), array(
		// 	'ping' => $_SERVER['REQUEST_TIME'],
		// 	'name' => $_SESSION['Company']['name'],
		// 	'stat' => 'shut',
		// ));

		$data = [];
		$data['Page'] = [
			'title' => 'POS :: Terminal :: Shut',
		];

		return $RES->write( $this->render('pos/terminal/shut.php', $data) );

	}
}
