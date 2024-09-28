<?php
/**
 * Test Case Base
 * Lower Level Unit Tester with HTTP Handlers
 */

namespace OpenTHC\POS\Test;

class Base extends \OpenTHC\Test\Base {

	protected $ghc; // API Guzzle HTTP Client
	protected $raw; // Raw Response Buffer

	// protected function setUp() : void
	// {
	// 	// $this->ghc = $this->_api();
	// }


	/**
		HTTP Utility
	*/
	function get($url)
	{
		$res = $this->ghc->get($url);
		$ret = $this->assertValidResponse($res, 200);
		return $ret;
	}


	/**
		HTTP Utility
	*/
	function post($url, $arg)
	{
		$res = $this->ghc->post($url, array('json' => $arg));
		return $res;
	}

	function _data_stash_get()
	{
		if (is_file($f)) {
			if (is_readable($f)) {
				$x = file_get_contents($f);
				$x = json_decode($x, true);
				return $x;
			}
		}

		return null;

	}

	function _data_stash_put($f, $d)
	{
		if (!is_string($d)) {
			$d = json_encode($d);
		}

		return file_put_contents($f, $d);
	}

}
