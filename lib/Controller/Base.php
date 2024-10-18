<?php
/**
 * Base Controller
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Controller;

class Base extends \OpenTHC\Controller\Base
{

	/**
	 * Decrypt the Token
	 */
	function open_auth_box($cpk, $box) : array
	{
		// $cpk = $m[1];
		// $box = $m[2];
		$box = \OpenTHC\Sodium::b64decode($box);

		$ssk = \OpenTHC\Config::get('openthc/pos/secret');
		$act = \OpenTHC\Sodium::decrypt($box, $ssk, $cpk);
		if (empty($act)) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-025]', 403);
		}
		$act = json_decode($act);
		if (empty($act)) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-029]', 403);
		}
		if (sodium_compare($act->pk, $cpk) !== 0) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-032]', 403);
		}

		// Time Check
		$dt0 = new \DateTime();
		$dt1 = \DateTime::createFromFormat('U', $act->ts);
		$age = $dt0->diff($dt1, true);
		if (($age->d != 0) || ($age->h != 0) || ($age->i > 5)) {
			throw new \Exception('Authentication Box Expired [PCB-040]', 400);
		}

		if (empty($act->contact)) {
			throw new \Exception('Authentication Box Data Corrupted [PCB-103]', 403);
		}

		if (empty($act->company)) {
			throw new \Exception('Authentication Box Data Corrupted [PCB-110]', 403);
		}

		return $act;
	}

	function findContact($id)
	{
		// Contact
		$Contact = $this->dbc->fetchRow('SELECT id, username FROM auth_contact WHERE id = :c0', [ ':c0' => $id ]);
		if (empty($Contact['id'])) {
			throw new \Exception('Authentication Box Invalid Authentication [PCB-095]', 403);
		}

		return $Contact;
	}

	function findCompany($id)
	{
		// Company
		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $act->company ]);
		if (empty($Company['id'])) {
			throw new \Exception('Authentication Box Invalid Authentication [PCB-095]', 403);
		}

		if (empty($Company['dsn'])) {
			throw new \Exception('Authentication Box Invalid Configuration [PCB-072]', 501);
		}

		return $Company;
	}


	/**
	 *
	 */
	function findService($pk)
	{
		// Check Redis
		$rdb = $this->Redis;

		// Check Database
		// v0
		$Service = $this->dbc->fetchRow('SELECT * FROM auth_service WHERE code = :s0', [
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
			$Keypair = $this->dbc->fetchRow($sql, [ ':pk' => $pk ]);
			if ( ! empty($Keypair['id'])) {
				$Service = $this->dbc->fetchRow('SELECT * FROM auth_service WHERE id = :s0', [
					':s0' => $Keypair['service_id'],
				]);
			}
		}

		if (empty($Service['id'])) {
			throw new \Exception('Authentication Box Service Not Found [PCB-084]', 403);
		}

		return $Service;

	}

}
