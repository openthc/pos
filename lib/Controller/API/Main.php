<?php
/**
 *
 */

namespace App\Controller\API;

class Main extends \App\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		__exit_json([
			'data' => null,
			'meta' => [],
		], 501);

	}
}
