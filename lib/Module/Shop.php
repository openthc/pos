<?php
/**
 * Shop Module
 */

namespace App\Module;

class Shop extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Shop\Main');

		$a->get('/cart', 'App\Controller\Shop\Cart');
		$a->post('/cart', 'App\Controller\Shop\Cart:post');

		$a->get('/checkout', 'App\Controller\Shop\Checkout');
		$a->post('/checkout', 'App\Controller\Shop\Checkout:post');

		$a->get('/example', 'App\Controller\Shop\Example');
	}
}
