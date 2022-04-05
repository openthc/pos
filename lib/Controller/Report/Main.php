<?php
/**
 * SPDX-License-Identifier: GPL-3.0-only
*/

namespace App\Controller\Report;

use Edoceo\Radix\Session;

class Main extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		__exit_html('<h1>Reports Not Available</h1>');
	}

	/**
	 *
	 */
	function recent($REQ, $RES, $ARG)
	{
		__exit_html('<h1>Requires More Sale History [CRM-019]</h1>');
	}

}
