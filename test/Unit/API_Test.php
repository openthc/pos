<?php
/**
 * Test The API
 */

namespace OpenTHC\POS\Test\Unit;

class API_Test extends \OpenTHC\POS\Test\Base
{
	function test_b2c_sale_create()
	{
		$b2c_sale = [];

		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
				'openthc-contact-id' => '',
				'openthc-company-id' => '',
				'openthc-license-id' => '',
			],
			'json' => [
				'id' => '',
				'created_at' => '',
			]
		];
		$res = $this->client->post('/api/v2018/b2c', $arg);
		$res = $this->assertValidResponse($res);

		var_dump($res);

		return $b2c_sale;
	}

	/**
	 * @depends test_b2c_sale_create
	 */
	function test_b2c_sale_item_create($b2c_sale)
	{
		$url = sprintf('/api/v2018/b2c/%s/item', $b2c_sale->id);
		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
			],
			'json' => [
				'inventory' => [ 'id' => '' ],
				'unit_count' => 1,
				'unit_price' => 2,
			]
		];

		$res = $this->client->post($url, $arg);
		$res = $this->assertValidResponse($res);

		var_dump($res);

		return $b2c_sale;
	}

	/**
	 * @depends test_b2c_sale_item_create
	 */
	function test_b2c_sale_detail($b2c_sale)
	{
		$url = sprintf('/api/v2018/b2c/%s', $b2c_sale->id);
		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
			],
		];
		$res = $this->client->get($url);
		$res = $this->assertValidResponse($res);

		var_dump($res);

		return $b2c_sale;
	}

	/**
	 * @depends test_b2c_sale_detail
	 */
	function test_b2c_sale_verify($b2c_sale)
	{
		$url = sprintf('/api/v2018/b2c/%s/verify', $b2c_sale->id);
		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
			],
		];
		$res = $this->client->post($url);
		$res = $this->assertValidResponse($res);

		return $b2c_sale;
	}

	/**
	 * @depends test_b2c_sale_verify
	 */
	function test_b2c_sale_commit($b2c_sale)
	{
		$url = sprintf('/api/v2018/b2c/%s/commit', $b2c_sale->id);
		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
			],
			'json' => [
				'key' => 'val',
			]
		];
		$res = $this->client->post($url);
		$res = $this->assertValidResponse($res);

		return $b2c_sale;
	}

	/**
	 * @depends test_b2c_sale_commit
	 */
	function test_b2c_sale_commit_fail($b2c_sale)
	{
		$url = sprintf('/api/v2018/b2c/%s/commit', $b2c_sale->id);
		$arg = [
			'headers' => [
				'Authorization' => sprintf('Bearer %s', $key),
			],
			'json' => [
				'key' => 'val',
			]
		];
		$res = $this->client->post($url);
		$res = $this->assertValidResponse($res, 409);

		return $b2c_sale;

	}

}

# -X POST \
# --header 'content-type: application/json' \
# --data '{ "Email": "npc@nationalpaymentcard.com", "MerchantID": "TEST", "PIN": "1234", "ReturnPseudoCardNumber": true }'

#Response:-
# {
#"data": null,
#"meta": {
#"detail": "API query '/api/v2017/b2c/commit' was not understood [CA7#024]"
#}
#}

# Create B2C Sale Record
# curl 'https://app.djb.openthc.dev/api/v2017/b2c' \
#   -X 'POST' \
#   --header 'authorization: Bearer uEu72zR6brePjYdt6J2pIBzf5SfcJ9K4ccz0Q1AELI65EAW88QbBuFOXDUUEA_g7' \
#   --header 'content-type: application/json' \
#   --data '{ "license": { "id": "$OPENTHC_LICENSE" } }'

# # Add Item to B2C Sale Record
# curl 'https://app.djb.openthc.dev/api/v2017/b2c/item' \
#   -X 'POST' \
#   --header 'authorization: Bearer uEu72zR6brePjYdt6J2pIBzf5SfcJ9K4ccz0Q1AELI65EAW88QbBuFOXDUUEA_g7' \
#   --header 'content-type: application/json' \
#   --data '{ "license": { "id": "$OPENTHC_LICENSE" } , "b2c": { "id": $B2C_SALE_ID }, "lot": { "id": "01FCH7F3AADC2BENAM4V7JBT8Q" }, "unit_count": 5, "unit_price": 5 }'

# # Commit
# curl 'https://app.djb.openthc.dev/api/v2017/b2c/commit' \
#   -X 'POST' \
#   --header 'authorization: Bearer uEu72zR6brePjYdt6J2pIBzf5SfcJ9K4ccz0Q1AELI65EAW88QbBuFOXDUUEA_g7' \
#   --header 'content-type: application/json' \
#   --data '{ "license": { "id": "$OPENTHC_LICENSE" } , "b2c": { "id": $B2C_SALE_ID } }'
