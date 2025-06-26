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
			'base_uri' => getenv('OPENTHC_TEST_ORIGIN')
		]);
	}

	function makeBearerToken()
	{
		// Client Public Key
		$cpk = \OpenTHC\Config::get('openthc/pos/public');
		// Client Secret Key
		$csk = \OpenTHC\Config::get('openthc/pos/secret');
		// Server Public Key (same as Client when requesting to self)
		$spk = \OpenTHC\Config::get('openthc/pos/public');

		// Crypto Box of Data
		$box = json_encode([
			'pk' => $cpk,
			'ts' => time(),
			'contact' => getenv('OPENTHC_POS_CONTACT0_ID'),
			'company' => getenv('OPENTHC_POS_CONTACT0_COMPANY_ID'),
			'license' => getenv('OPENTHC_POS_CONTACT0_COMPANY_LICENSE_ID'),
		]);
		$box = \OpenTHC\Sodium::encrypt($box, $csk, $spk);
		$box = \OpenTHC\Sodium::b64encode($box);

		return sprintf('Bearer v2024/%s/%s', $cpk, $box);
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
