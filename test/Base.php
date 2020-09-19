<?php
/**
 * Test Case Base
 * Lower Level Unit Tester with HTTP Handlers
 */

namespace Test;

class Base extends \PHPUnit\Framework\TestCase
{
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

	/**
	 * Intends to become an assert wrapper for a bunch of common response checks
	 * @param $res, Response Object
	 * @return void
	 */
	function assertValidResponse($res, $code=200, $dump=null)
	{
			$this->raw = $res->getBody()->getContents();

			$hrc = $res->getStatusCode();

			if (empty($dump)) {
					if ($code != $hrc) {
							$dump = "HTTP $hrc != $code";
					}
			}

			if (!empty($dump)) {
					echo "\n<<< $dump <<< $hrc <<<\n{$this->raw}\n###\n";
			}

			$ret = \json_decode($this->raw, true);

			$this->assertEquals($code, $res->getStatusCode());
			$type = $res->getHeaderLine('content-type');
			$type = strtok($type, ';');
			$this->assertEquals('application/json', $type);
			$this->assertIsArray($ret);

			// $this->assertEmpty($ret['status']);
			// $this->assertEmpty($ret['result']);

			return $ret;
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
