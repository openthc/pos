<?php
/**
 * Test our Configuration
 */

class Config_Test extends \Test\Base
{
	function test_config_file()
	{
		$f = sprintf('%s/etc/app.ini', APP_ROOT);
		$this->assertTrue(is_file($f), 'No Config File');
	}

	function test_service_config()
	{
		$cfg = \App_Config::get('');
		// var_dump($cfg);

		$this->assertIsArray($cfg);
		$this->assertArrayHasKey('application', $cfg);
		// $this->assertArrayHasKey('openthc_app', $cfg);
		$this->assertArrayNotHasKey('openthc_cic', $cfg);
		$this->assertArrayNotHasKey('openthc_dir', $cfg);
		// $this->assertArrayNotHasKey('openthc_lab', $cfg);
		// $this->assertArrayNotHasKey('openthc_menu', $cfg);
		$this->assertArrayNotHasKey('openthc_ops', $cfg);
		$this->assertArrayNotHasKey('openthc_sso', $cfg);
		$this->assertArrayNotHasKey('metabase', $cfg);
		$this->assertArrayNotHasKey('redis', $cfg);
		$this->assertArrayNotHasKey('statsd', $cfg);

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

			'metabase/hostname',
			'metabase/username',
			'metabase/password',
			'metabase/embedkey',

			'openthc/cic/hostname',

			'openthc/dir/hostname',
			'openthc/dir/public',
			'openthc/dir/secret',

			'openthc/lab/hostname',
			'openthc/lab/public',
			'openthc/lab/secret',

			'openthc/pos/hostname',

			'openthc/b2b/hostname', // Menu New Name
			'openthc/menu/hostname', // Menu Old Name

			'openthc/ops/hostname',
			'openthc/ops/public',
			'openthc/ops/secret',

			'openthc/sso/hostname',
			'openthc/sso/public',
			'openthc/sso/secret',

		];

		foreach ($key_list as $k) {
			$f = sprintf('%s/etc/%s', APP_ROOT, $k);
			$this->assertTrue(is_file($f), sprintf('Missing Config: "%s"', $k));
		}


	}

	function test_api()
	{
		$cfg = \App_Config::get('api');
		$this->assertIsArray($cfg);

		$this->assertArrayHasKey('authhash', $cfg);
		$this->assertNotEmpty($cfg['authhash']);

		$this->assertArrayHasKey('hostname', $cfg);
		$this->assertNotEmpty($cfg['hostname']);
	}

	function test_redis()
	{
		// @todo Check for Redis Config and try to connect
		$h = \OpenTHC\Config::get('redis/hostname');
		$this->assertNotEmpty($h);

		$k = _random_hash();
		Service_Redis::set($k, 'TEST');
		$v = Service_Redis::get($k);
		$this->assertEquals('TEST', $v);

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
