<?php
/**
 * Test Case Base
 * Lower Level Unit Tester with HTTP Handlers
 */

namespace OpenTHC\POS\Test;

class Base extends \OpenTHC\Test\Base {

	protected $ghc; // API Guzzle HTTP Client

	function setup() : void
	{
		$this->client = $this->getGuzzleClient([
			'base_uri' => OPENTHC_TEST_ORIGIN
		]);
	}

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

}
