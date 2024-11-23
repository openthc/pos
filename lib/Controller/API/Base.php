<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 *
 */

namespace OpenTHC\POS\Controller\API;

class Base extends \OpenTHC\Controller\Base
{
	use \OpenTHC\POS\Traits\OpenAuthBox;
	use \OpenTHC\Traits\FindService;
	use \OpenTHC\Traits\FindContact;
	use \OpenTHC\Traits\FindCompany;
	use \OpenTHC\Traits\FindLicense;

	function __construct($c)
	{
		$c['phpErrorHandler'] = function($c) {
			return function($REQ, $RES, $ERR) {
				__exit_json([
					'data' => $ERR,
					'meta' => [ 'note' => 'Fatal Error [ERR-001]' ]
				], 500);
			};
		};

		$c['errorHandler'] = function($c) {
			return function($REQ, $RES, $ERR) {

				// $ERR is an Exception

				$ret_code = 500;
				$err_code = $ERR->getCode();

				__exit_json([
					'data' => null,
					'meta' => [
						'note' => 'Fatal Error [ERR-002]',
						'error' => $ERR->getMessage(),
					]
				], $err_code ?: $ret_code);
			};
		};

		// Over-ride the Response object
		// FAIL: Pimple\Exception\FrozenServiceException: Cannot override frozen service "response".
		// $c['response'] = function($c) {
		// 	$ret = new class extends \Slim\Http\Response {
		// 		function withJSON($json, $code=200, $flag=0)
		// 		{
		// 			$flag = intval($flag);
		// 			return parent::withJSON($json, $code, ($flag | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		// 		}
		// 	};
		// 	return $ret;
		// };

		parent::__construct($c);

	}

	/**
	 * This maybe should be in Middleware
	 */
	function auth_parse()
	{
		$auth = $_SERVER['HTTP_AUTHORIZATION'];
		if ( ! preg_match('/^Bearer v2024\/([\w\-]{43})\/([\w\-]+)$/', $auth, $m)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Authorization [CAB-068]' ],
			), 403);
		}

		$act = $this->open_auth_box($m[1], $m[2]);

		$dbc_auth = _dbc('auth');

		// Find Service Lookup CPK and See if we Trust Them
		$Service = $this->findService($dbc_auth, $act->pk);
		$Contact = $this->findContact($dbc_auth, $act->contact);
		$Company = $this->findCompany($dbc_auth, $act->company);

		$dbc_user = _dbc($Company['dsn']);
		$License = $this->findLicense($dbc_user, $act->license);

		$this->Service = $Service;
		$this->Contact = $Contact;
		$this->Company = $Company;
		$this->License = $License;

	}

	/**
	 *
	 */
	function failure($RES, $text, $code=500)
	{
		return $RES->withJSON([
			'data' => null
			, 'meta' => [ 'note' => $text ]
		], $code, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$this->auth_parse();

		__exit_json([
			'data' => $_SERVER,
			'meta' => [ 'note' => 'Not Implemented [CAB-098]' ],
		], 501);

	}
}
