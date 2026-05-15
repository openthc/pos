<?php
/**
 * Test the Print Queue
 */

namespace OpenTHC\POS\Test\Core;

class Print_Queue_Test extends \OpenTHC\POS\Test\Base
{
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
