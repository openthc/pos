<?php
/**
 * Find Service or Throw
 */

namespace OpenTHC\POS\Traits;

trait FindService
{
	function findService(string $s0)
	{
		// Check Redis
		$rdb = $this->Redis;

		// Check Database
		// v0
		$Service = $this->dbc->fetchRow('SELECT * FROM auth_service WHERE code = :s0', [
			':s0' => $s0,
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
			$Keypair = $this->dbc->fetchRow($sql, [ ':pk' => $s0 ]);
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
