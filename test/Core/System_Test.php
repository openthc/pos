<?php
/**
 * Test Expected System Commands
 */

namespace OpenTHC\POS\Test\Core;

class System_Test extends \OpenTHC\POS\Test\Base
{
	function test_convert()
	{
		$f = '/usr/bin/convert';
		$this->assertTrue(is_file($f), '/usr/bin/convert not found');
		$this->assertTrue(is_executable($f), '/usr/bin/convert not executable');
	}

	function test_convert_policy()
	{
		// Read the Convert Policy XML File
		// Make sure that PS and PDF *ARE* Allowed
		$xml_file = '/etc/ImageMagick-6/policy.xml';
		$this->assertTrue(is_file($xml_file), 'No File');

		$xml_data = simplexml_load_file($xml_file);
		// Do the tright thing here
	}

	function test_exiftool()
	{
		$f = '/usr/bin/exiftool';
		$this->assertTrue(is_file($f));
		$this->assertTrue(is_executable($f));
	}

	function test_filesytem()
	{
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

	function test_gs()
	{
		$f = '/usr/bin/gs';
		$this->assertTrue(is_file($f), '/usr/bin/gs does not exist');
		$this->assertTrue(is_executable($f), '/usr/bin/gs is not executable');
	}

	function test_pdfunite()
	{
		$f = '/usr/bin/pdfunite';
		$this->assertTrue(is_file($f));
		$this->assertTrue(is_executable($f));
	}

	// function test_puppeteer()
	// {
	// 	$f = '/usr/bin/node';
	// 	$this->assertTrue(is_file($f), '/usr/bin/node does not exist');
	// 	$this->assertTrue(is_executable($f), '/usr/bin/node is not Executable');

	// 	$f = sprintf('%s/node_modules/puppeteer/index.js', APP_ROOT);
	// 	$this->assertTrue(is_file($f), 'puppeteer/index.js does not exist');
	// }

}
