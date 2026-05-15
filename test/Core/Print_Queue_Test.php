<?php
/**
 * Test the Print Queue
 */

namespace OpenTHC\POS\Test\Core;

class Print_Queue_Test extends \OpenTHC\POS\Test\Base
{
	static function setUpBeforeClass() : void
	{
		parent::setUpBeforeClass();
		// @todo Seed a print queue manually here
	}

	function zzz_test()
	{
		/*
		@see view/settings/b2c/printer.php
		$sql = <<<SQL
		SELECT * FROM auth_company_option WHERE key LIKE 'print-queue%'
		SQL;

		$res_print_queue = SQL::fetch_all($sql);
		foreach ($res_print_queue as $row) {
			// find well-known
		}
		*/
		$dbc = _dbc('user');
		$queue = $dbc->fetchall('well known print queue');
	}

	// @see lib/Controller/API/PrintQueue.php
	function test_read_print_queue()
	{
		$rdb = \OpenTHC\Service\Redis::factory();
		$key0 = '/global/print-queue/*'; // get everything
		$keys = $rdb->keys($key0);
		$this->assertNotEmpty($keys);
		foreach ($keys as $k) {
			$data = $rdb->hgetall($k);
			// var_dump($data);
		}
	}
}
