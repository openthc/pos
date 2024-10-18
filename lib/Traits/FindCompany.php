<?php
/**
 * Find a Company or Throw
 */

namespace OpenTHC\POS\Traits;

trait FindCompany
{
	function findCompany(string $c0)
	{
		$sql = <<<SQL
		SELECT id, name, dsn
		FROM auth_company
		WHERE id = :c0
		SQL;

		$arg = [
			':c0' => $c0
		];

		$Company = $this->dbc->fetchRow($sql, $arg);
		if (empty($Company['id'])) {
			throw new \Exception('Authentication Box Invalid Authentication [PCB-095]', 403);
		}

		if (empty($Company['dsn'])) {
			throw new \Exception('Authentication Box Invalid Configuration [PCB-072]', 501);
		}

		return $Company;

	}
}
