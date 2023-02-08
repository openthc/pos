<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 *
 */

namespace OpenTHC\POS\Controller\API\B2C;

class Payment extends \OpenTHC\POS\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		if ( ! preg_match('/^01\w{24}$/', $ARG['id'])) {
			return $this->failure($RES, 'Invalid Request [ABP-015]', 400);
		}

		$source_data = $this->parseJSON();
		if (empty($source_data['license']['id'])) {
			return $this->failure($RES, 'Invalid License ID [ABP-020]', 400);
		}

		$dbc = _dbc($this->Company['dsn']);

		$b2c = new \OpenTHC\POS\B2C\Sale($dbc);
		if (empty($b2c['id'])) {
			return $this->failure($RES, 'Not Found [ABP-027]', 404);
		}

		// $b2c_ledger = new
		$b2c_ledger['']

	}
}
