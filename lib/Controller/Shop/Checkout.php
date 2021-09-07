<?php
/**
 * Shop Checkout
 */

namespace App\Controller\Shop;

class Checkout extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = $this->data;

		$key = sprintf('b2b-sale-%s', $_GET['o']);
		$data['b2b_sale'] = $_SESSION[$key];

		$data['Page']['title'] = sprintf('Checkout :: %s', $data['b2b_sale']['company']['name']);

		// $data['product_list'] = $this->load_cart_product_list($_GET['c']);
		$html = $this->render('shop/checkout.php', $data);

		return $RES->write($html);
	}

	/**
	 *
	 */
	function done($REQ, $RES, $ARG)
	{
		$data = $this->data;

		$key = sprintf('b2b-sale-%s', $_GET['o']);
		$data['b2b_sale'] = $_SESSION[$key];

		$data['Page']['title'] = sprintf('Checkout :: %s', $data['b2b_sale']['company']['name']);

		$html = $this->render('shop/checkout-done.php', $data);

		return $RES->write($html);

	}

	/**
	 *
	 */
	function post($REQ, $RES, $ARG)
	{
		// $data = $this->data;
		return $RES->withRedirect(sprintf('/shop/checkout/done?o=%s', $_GET['o']));

	}

}
