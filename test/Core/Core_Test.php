<?php
/**
 *
 */

namespace OpenTHC\POS\Test\Core;

class Core_Test extends \OpenTHC\POS\Test\Base
{
	public function test_dependencies()
	{
		// Check for Redis?

		// Check for StatsD?

		$dir = sprintf('%s/webroot', APP_ROOT);
		$this->assertTrue(is_dir($dir));

		// Var Path
		$var = sprintf('%s/var', APP_ROOT);
		$this->assertTrue(is_dir($var));

		$var_stat = stat($var);

		$o = posix_getpwuid($var_stat[4]);
		$this->assertIsArray($o);
		$this->assertEquals('openthc', $o['name']);

		$g = posix_getgrgid($var_stat[5]);
		$this->assertIsArray($g);
		$this->assertEquals('www-data', $g['name']);

		$m = ($var_stat[2] & 0x0fff);
		$this->assertEquals($m, 0775); // Perms in OCTAL

		// Webroot Output Path
		$dir = sprintf('%s/webroot/output', APP_ROOT);
		$dir_stat = stat($dir);

		$o = posix_getpwuid($dir_stat[4]);
		$this->assertIsArray($o);
		$this->assertEquals('openthc', $o['name']);

		$g = posix_getgrgid($dir_stat[5]);
		$this->assertIsArray($g);
		$this->assertEquals('www-data', $g['name']);

		$m = ($dir_stat[2] & 0x0fff);
		$this->assertEquals($m, 0775); // Perms in OCTAL

		// @todo Check all the other directories/files to be owned by 'openthc'

		// $this->assertIsTrue();
	}
}
