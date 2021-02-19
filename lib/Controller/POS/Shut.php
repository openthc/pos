<?php
/**
 * Register/Terminal Shut
*/

namespace App\Controller\POS;

use Edoceo\Radix\Session;

class Shut extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$R = $this->_container->Redis;
		$R->hset(sprintf('pos-terminal-%s', $_SESSION['pos-terminal-id']), array(
			'ping' => $_SERVER['REQUEST_TIME'],
			'name' => $_SESSION['Company']['name'],
			'stat' => 'shut',
		));

		unset($_SESSION['pos-terminal-id']);
		unset($_SESSION['pos-terminal-contact']);

		Session::flash('info', 'Signed out of POS Terminal');

		return $RES->withRedirect('/dashboard');

	}
}
