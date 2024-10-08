<?php
/**
 * Test our Configuration
 */

namespace OpenTHC\POS\Test\Core;

class Config_Test extends \OpenTHC\POS\Test\Base
{
	function test_config_file()
	{
		$f = sprintf('%s/etc/config.php', APP_ROOT);
		$this->assertTrue(is_file($f), 'No Config File');
	}

	function test_service_config()
	{
		$cfg = \OpenTHC\Config::get('');
		// var_dump($cfg);

		$this->assertIsArray($cfg);

		$key_list = [
			'database/auth/hostname',
			'database/auth/username',
			'database/auth/password',
			'database/auth/database',

			'database/main/hostname',
			'database/main/username',
			'database/main/password',
			'database/main/database',

			'redis/hostname',

			'statsd/hostname',

			// 'metabase/hostname',
			// 'metabase/username',
			// 'metabase/password',
			// 'metabase/embedkey',

			'openthc/dir/origin',
			'openthc/dir/public',

			'openthc/pos/origin',
			'openthc/pos/public',
			'openthc/pos/secret',
			'openthc/pos/client-id',
			'openthc/pos/client-sk',

			'openthc/b2b/origin', // Menu New Name

			// 'openthc/ops/origin',
			// 'openthc/ops/public',
			// 'openthc/ops/secret',

			'openthc/sso/origin',
			'openthc/sso/public',

		];

		foreach ($key_list as $k) {
			$v = \OpenTHC\Config::get($k);
			$this->assertNotEmpty($v, sprintf('Config %s is missing', $k));
		}

		$key_list = [
			'openthc/app/secret',
			'openthc/b2b/secret',
			'openthc/cre/secret',
			'openthc/dir/secret',
			'openthc/lab/secret',
			'openthc/pipe/secret',
			'openthc/sso/secret',
		];

		foreach ($key_list as $k) {
			$v = \OpenTHC\Config::get($k);
			$this->assertEmpty($v, sprintf('Config %s should be empty/unset', $k));
		}

	}

	/**
	 *
	 */
	function test_api()
	{
		$cfg = \OpenTHC\Config::get('openthc/pos');
		$this->assertIsArray($cfg);

		$this->assertArrayHasKey('client-id', $cfg);
		$this->assertNotEmpty($cfg['client-id']);

		$this->assertArrayHasKey('client-pk', $cfg);
		$this->assertNotEmpty($cfg['client-pk']);

		$this->assertArrayHasKey('client-sk', $cfg);
		$this->assertNotEmpty($cfg['client-sk']);

	}

	/**
	 *
	 */
	function test_redis()
	{
		// @todo Check for Redis Config and try to connect
		$h = \OpenTHC\Config::get('redis/hostname');
		$this->assertNotEmpty($h);

		$k = _random_hash();
		$r = new \Redis();
		$r->connect($h);
		$r->set($k, 'TEST');

		$x = $r->get($k);
		$this->assertEquals('TEST', $x);

	}

	function test_statsd()
	{
		// @todo Check for Config & functions?
		$h = \OpenTHC\Config::get('statsd/hostname');
		$this->assertNotEmpty($h);

		$this->assertTrue(function_exists('_stat_counter'));
		$this->assertTrue(function_exists('_stat_gauge'));
		$this->assertTrue(function_exists('_stat_timer'));

	}
}
