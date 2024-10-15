<?php
/**
 * POS Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class POS extends \OpenTHC\Module\Base
{
	/**
	 *
	 */
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\POS\Main');
		$a->post('', 'OpenTHC\POS\Controller\POS\Main:post');

		$a->get('/fast', 'OpenTHC\POS\Controller\POS\Fast');
		$a->get('/open', function($REQ, $RES, $ARG) {
			unset($_SESSION['Cart']);
			return $RES->withRedirect('/pos');
		});

		$a->map([ 'GET', 'POST', 'DELETE' ], '/ajax', 'OpenTHC\POS\Controller\POS\Ajax');

		$a->post('/checkout/commit', 'OpenTHC\POS\Controller\POS\Checkout\Commit');
		$a->get('/checkout/done', 'OpenTHC\POS\Controller\POS\Checkout\Done');

		$a->get('/checkout/open', 'OpenTHC\POS\Controller\POS\Checkout\Open');
		$a->post('/checkout/open', 'OpenTHC\POS\Controller\POS\Checkout\Open:post');

		$a->map([ 'GET', 'POST' ], '/checkout/receipt', 'OpenTHC\POS\Controller\POS\Checkout\Receipt');

		$a->post('/cart/ajax', 'OpenTHC\POS\Controller\POS\Cart\Ajax');
		$a->post('/cart/drop', 'OpenTHC\POS\Controller\POS\Cart\Drop');
		$a->post('/cart/save', 'OpenTHC\POS\Controller\POS\Cart\Save');

		$a->get('/delivery', 'OpenTHC\POS\Controller\POS\Delivery');
		$a->map([ 'GET', 'POST' ], '/delivery/ajax', 'OpenTHC\POS\Controller\POS\Delivery:ajax');

		$a->get('/online', 'OpenTHC\POS\Controller\POS\Online');

		$a->get('/shut', 'OpenTHC\POS\Controller\POS\Shut');
	}

}
