<?php
/**
 * Find a Contact or Throw
 */

namespace OpenTHC\POS\Traits;

trait FindContact
{
	function findContact(string $c0)
	{
		$sql = <<<SQL
		SELECT id, username
		FROM auth_contact
		WHERE id = :c0
		SQL;

		$arg = [ ':c0' => $c0 ];

		$Contact = $this->dbc->fetchRow($sql, $arg);
		if (empty($Contact['id'])) {
			throw new \Exception('Authentication Box Invalid Authentication [PCB-095]', 403);
		}

		return $Contact;

	}
}
