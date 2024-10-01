<?php
/**
 * Test The Webhook?
 */

namespace OpenTHC\POS\Test\Webhook;

class Weedmaps_Test extends
{

	function test_draft()
	{
		$url = sprintf('%s/webhook/weedmaps/order', OPENTHC_TEST_ORIGIN);
		$req = _curl_init($url);
		$res = _curl_post_json($url, $body, $head);



	}

	function test_pending()
	{
		$url = sprintf('%s/webhook/weedmaps/order', OPENTHC_TEST_ORIGIN);
		$req = _curl_init($url);
		$res = _curl_post_json($url, $body, $head);


	}

}
