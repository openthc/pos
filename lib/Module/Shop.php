<?php
/**
 * Shop Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class Shop extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\Shop\Main');

		$a->get('/cart', 'OpenTHC\POS\Controller\Shop\Cart');
		$a->post('/cart', 'OpenTHC\POS\Controller\Shop\Cart:post');

		$a->get('/checkout', 'OpenTHC\POS\Controller\Shop\Checkout');
		$a->post('/checkout', 'OpenTHC\POS\Controller\Shop\Checkout:post');

		$a->get('/checkout/done', 'OpenTHC\POS\Controller\Shop\Checkout:done');

		$a->get('/example', 'OpenTHC\POS\Controller\Shop\Example');
	}
}
