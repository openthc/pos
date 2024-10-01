<?php
/**
 * Test The Webhook?
 */

namespace OpenTHC\POS\Test\Webhook;

class Weedmaps_Test extends \OpenTHC\POS\Test\Base
{
	function test_draft()
	{
		$res = $this->client->post('/webhook/weedmaps/order', [ 'json' => [
			'status' => 'PENDING',
		]]);
		$this->assertValidResponse($res);
	}

	function test_pending()
	{
		$res = $this->client->post('/webhook/weedmaps/order', [ 'json' => [
			'status' => 'PENDING',
		]]);
		$this->assertValidResponse($res);
	}

}
