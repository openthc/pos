<?php
/**
 * POS Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Module;

class POS extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->get('', 'App\Controller\POS\Main');
		$a->post('', 'App\Controller\POS\Main:post');

		$a->get('/fast', 'App\Controller\POS\Fast');
		$a->get('/open', function($REQ, $RES, $ARG) {
			unset($_SESSION['Checkout']);
			return $RES->withRedirect('/pos');
		});

		$a->map([ 'GET', 'POST', 'DELETE' ], '/ajax', 'App\Controller\POS\Ajax');

		$a->post('/checkout/commit', 'App\Controller\POS\Checkout\Commit');
		$a->get('/checkout/done', 'App\Controller\POS\Checkout\Done');

		$a->get('/checkout/open', 'OpenTHC\POS\Controller\POS\Checkout\Open');
		$a->post('/checkout/open', 'OpenTHC\POS\Controller\POS\Checkout\Open:post');

		$a->map([ 'GET', 'POST' ], '/checkout/receipt', 'App\Controller\POS\Checkout\Receipt');

		$a->post('/cart/ajax', 'App\Controller\POS\Cart\Ajax');
		$a->post('/cart/drop', 'App\Controller\POS\Cart\Drop');
		$a->post('/cart/save', 'App\Controller\POS\Cart\Save');

		$a->get('/delivery', 'App\Controller\POS\Delivery');
		$a->map([ 'GET', 'POST' ], '/delivery/ajax', 'App\Controller\POS\Delivery:ajax');

		$a->get('/online', 'App\Controller\POS\Online');

		$a->get('/shut', 'App\Controller\POS\Shut');
	}

}
