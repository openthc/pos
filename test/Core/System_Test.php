<?php
/**
 * Test Expected System Commands
 */

namespace OpenTHC\POS\Test\Core;

class System_Test extends \Test\Base
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


	function test_gs()
	{
		$f = '/usr/bin/gs';
		$this->assertTrue(is_file($f), '/usr/bin/gs does not exist');
		$this->assertTrue(is_executable($f), '/usr/bin/gs is not executable');
	}

	/**
	 * We Ditched this in favor of GS
	 */
	function test_pdftk()
	{
		$f = '/usr/bin/pdftk';
		$this->assertFalse(is_file($f), 'pdftk should be removed');
/*
# pdftk --help
pdftk port to java 3.1.1 a Handy Tool for Manipulating PDF Documents
Copyright (c) 2017-2018 Marc Vinyals - https://gitlab.com/pdftk-java/pdftk
Copyright (c) 2003-2013 Steward and Lee, LLC.
pdftk includes a modified version of the iText library.
Copyright (c) 1999-2009 Bruno Lowagie, Paulo Soares, et al.
This is free software; see the source code for copying conditions. There is
*/
		// $buf = shell_exec('/usr/bin/pdftk --help 2>&1');
		// $this->assertStringContainsString('pdftk port to java 3.1.1', $buf);
	}

	function test_pdfunite()
	{
		$f = '/usr/bin/pdfunite';
		$this->assertTrue(is_file($f));
		$this->assertTrue(is_executable($f));
	}

	function test_puppeteer()
	{
		$f = '/usr/bin/node';
		$this->assertTrue(is_file($f), '/usr/bin/node does not exist');
		$this->assertTrue(is_executable($f), '/usr/bin/node is not Executable');

		$f = sprintf('%s/node_modules/puppeteer/index.js', APP_ROOT);
		$this->assertTrue(is_file($f), 'puppeteer/index.js does not exist');
	}

}
