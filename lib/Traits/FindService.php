<?php
/**
 * Find Service or Throw
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Traits;

trait FindService
{
	/**
	 *
	 */
	function findService(string $s0)
	{
		$Service = null;

		// Check Redis
		$rdb = $this->_container->Redis;
		$key = sprintf('/pos/auth/service/%s', $s0);

		// Cache
		$chk = $rdb->get($key);
		if ( ! empty($chk)) {
			$Service = json_decode($chk, true);
		}

		// Check Database
		// v0
		if (empty($Service['id'])) {
			$Service = $this->dbc->fetchRow('SELECT * FROM auth_service WHERE (id = :s0 OR code = :s0)', [
				':s0' => $s0,
			]);
		}

		// v1 -- Keypair
		if (empty($Service['id'])) {

			$sql = <<<SQL
			SELECT id, service_id
			FROM auth_service_keypair
			WHERE pk = :pk
			AND stat = 200
			AND deleted_at IS NULL
			AND (expires_at IS NULL OR expires_at <= now())
			SQL;
			$Keypair = $this->dbc->fetchRow($sql, [ ':pk' => $s0 ]);

			if ( ! empty($Keypair['id'])) {
				$Service = $this->dbc->fetchRow('SELECT * FROM auth_service WHERE id = :s0', [
					':s0' => $Keypair['service_id'],
				]);
			}
		}

		if (empty($Service['id'])) {
			throw new \Exception('Service Not Found [PCB-084]', 403);
		}

		$rdb->set($key, json_encode($Service), [ 'ex' => 240 ]);

		return $Service;

	}

}
