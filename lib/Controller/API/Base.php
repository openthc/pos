<?php
/**
 * (c) 2018 OpenTHC, Inc.
 * This file is part of OpenTHC API released under MIT License
 * SPDX-License-Identifier: GPL-3.0-only
 *
 */

namespace App\Controller\API;

class Base extends \OpenTHC\Controller\Base
{
	function __construct($c)
	{
		$c['phpErrorHandler'] = function($c) {
			return function($REQ, $RES, $ERR) {
				var_dump($ERR);
				exit(0);
				__exit_json([
					'data' => null
					, 'meta' => [ 'detail' => 'Fatal Error [ERR-001]' ]
				], 500);
			};
		};

		$c['errorHandler'] = function($x) {
			return function($REQ, $RES, $ERR) {
				// var_dump($ERR);
				__exit_json([
					'data' => null
					, 'meta' => [ 'detail' => 'Fatal Error [ERR-002]' ]
				], 500);
			};
			// var_dump($x);
			// echo "ERR-002";
			// exit(0);
		};

		// $c['notFoundHandler']
		// $c['response'] = new class extends \Slim\Http\Response {
		// 	function withJSON($json, $code=200, $flag=0)
		// 	{
		// 		$flag = intval($flag);
		// 		return parent::withJSON($json, $code, ($flag | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		// 	}
		// };

		parent::__construct($c);

	}

	/**
	 * This maybe should be in Middleware
	 */
	function auth_parse()
	{
		$auth = trim($_SERVER['HTTP_AUTHORIZATION']);
		if (empty($auth)) {
			$auth = 'Bearer ' . trim($_GET['bearer']);
		}

		// Prefer Bearer
		$tok = preg_match('/^Bearer (.+)$/', $auth, $m) ? $m[1] : null;
		if (empty($tok)) {
			// Token is the Legacy Way (and should be removed)
			$tok = preg_match('/^Token (.+)$/', $auth, $m) ? $m[1] : null;
		}

		if (empty($tok)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => 'Bearer Not Provided [CAA-048]' ],
			), 403);
		}

		$dbc_auth = _dbc('auth');

		$sql = 'SELECT meta FROM auth_context_ticket WHERE id = ?';
		$arg = array($tok);
		$res = $dbc_auth->fetchRow($sql, $arg);
		if (empty($res)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => "Bearer Token Not Found [CAA-060]" ],
			), 403);
		}

		$act = json_decode($res['meta'], true);
		if (empty($act)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => 'Bearer Not Valid [CAA-086]' ],
			), 403);
		}

		if (empty($act['company'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => 'Bearer Data Corrupted [CAA-076]' ],
			), 403);
		}

		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $act['company'] ]);
		if (empty($Company['id'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => 'Invalid Authentication [CAA-095]' ],
			), 403);
		}

		if (empty($Company['dsn'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'detail' => 'Invalid Configuration [CAA-072]' ],
			), 501);
		}

		$this->Company = $Company;
		$this->Contact = [];

		// Set Contact
		if (!empty($res['contact'])) {
			if (is_array($res['contact'])) { // v3
				$this->Contact = $res['contact'];
			} elseif (is_string($res['contact'])) {
				$this->Contact['id'] = $res['contact'];
			}
		}

	}

	/**
	 *
	 */
	function failure($RES, $text, $code=500)
	{
		return $RES->withJSON([
			'data' => null
			, 'meta' => [ 'detail' => $text ]
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
			'meta' => [ 'detail' => 'Not Implemented [CAB-098]' ],
		], 501);

	}
}
