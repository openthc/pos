<?php
/**
 * Contact AJAX Handler
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller;

class Contact extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		$res_contact = $dbc->fetchAll('SELECT id, fullname FROM contact WHERE guid LIKE :g0 OR code LIKE :c0', [
			':g0' => sprintf('%%%s%%', $_GET['term']),
			':c0' => sprintf('%%%s%%', $_GET['term']),
		]);

		$ret = [];
		foreach ($res_contact as $c) {
			$ret[] = [
				'id' => $c['id'],
				'label' => $c['fullname'],
				'value' => $c['fullname'],
			];
		}

		return $RES->withJSON($ret);

	}
}
