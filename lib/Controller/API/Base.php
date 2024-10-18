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

		$cpk = $m[1];
		$box = $m[2];
		$box = \OpenTHC\Sodium::b64decode($box);

		$ssk = \OpenTHC\Config::get('openthc/pos/secret');
		$act = \OpenTHC\Sodium::decrypt($box, $ssk, $cpk);
		if (empty($act)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Service Key [CAB-094]' ],
			), 403);
		}
		$act = json_decode($act);
		if (empty($act)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Service Key [CAB-101]' ],
			), 403);
		}
		if (sodium_compare($act->pk, $cpk) !== 0) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Service Key [CAB-107]' ],
			), 403);
		}

		// Time Check
		$dt0 = new \DateTime();
		$dt1 = \DateTime::createFromFormat('U', $act->ts);
		$age = $dt0->diff($dt1, true);
		if (($age->d != 0) || ($age->h != 0) || ($age->i > 5)) {
			return $RES->withStatus(400)->withJson([
				'data' => null,
				'meta' => [ 'note' => 'Invalid Date [MCA-110]' ]
			]);
		}

		if (empty($act->contact)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Bearer Data Corrupted [CAB-103]' ],
			), 403);
		}

		if (empty($act->company)) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Bearer Data Corrupted [CAB-110]' ],
			), 403);
		}

		$dbc_auth = _dbc('auth');

		// Find Service Lookup CPK and See if we Trust Them
		$Service = $this->findService($dbc_auth, $cpk);

		// Contact
		$Contact = $dbc_auth->fetchRow('SELECT id, username FROM auth_contact WHERE id = :c0', [ ':c0' => $act->contact ]);
		if (empty($Contact['id'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Authentication [CAB-095]' ],
			), 403);
		}

		// Company
		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $act->company ]);
		if (empty($Company['id'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Authentication [CAB-095]' ],
			), 403);
		}

		if (empty($Company['dsn'])) {
			__exit_json(array(
				'data' => null,
				'meta' => [ 'note' => 'Invalid Configuration [CAB-072]' ],
			), 501);
		}

		$this->Service = $Service;
		$this->Contact = $Contact;
		$this->Company = $Company;

		// License?

	}

	function findService($dbc, $pk)
	{
		// Check Redis
		$rdb = $this->Redis;

		// Check Database
		// v0
		$Service = $dbc->fetchRow('SELECT * FROM auth_service WHERE code = :s0', [
			':s0' => $pk,
		]);

		// v1 -- Keypair
		if (empty($Service['id'])) {

			$sql = <<<SQL
			SELECT id, service_id
			FROM auth_service_keypair
			WHERE pk = :pk
			AND deleted_at IS NULL
			AND (expires_at IS NULL OR expires_at <= now())
			SQL;
			$Keypair = $dbc->fetchRow($sql, [ ':pk' => $pk ]);
			if ( ! empty($Keypair['id'])) {
				$Service = $dbc->fetchRow('SELECT * FROM auth_service WHERE id = :s0', [
					':s0' => $Keypair['service_id'],
				]);
			}
		}

		if (empty($Service['id'])) {
			throw new \Exception('Service Not Found [CAB-084]', 403);
		}

		return $Service;

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
