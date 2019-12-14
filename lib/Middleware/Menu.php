<?php
/**
 * Show the Zero Menu
 */

namespace App\Middleware;

class Menu extends \OpenTHC\Middleware\Base
{
	function __invoke($REQ, $RES, $NMW)
	{
		$menu = array(
			'home_link' => '/',
			'home_html' => '<i class="fas fa-home"></i>',
			'show_search' => false,
			'main' => array(),
			'page' => array(
				array(
					'link' => '/auth/open?r=/home',
					'html' => '<i class="fas fa-sign-in-alt"></i>',
				)
			),
		);

		$auth = false;
		if (!empty($_SESSION['uid'])) {
			$auth = true;
		}
		if (!empty($_SESSION['pipe-token'])) {
			$auth = true;
		}

		if ($auth) {

			$menu['home_link'] = '/dashboard';
			$menu['main'] = array(
				array(
					'link' => '/pos',
					'html' => '<i class="fas fa-cash-register"></i> POS',
				),
				array(
					'link' => '/crm',
					'html' => '<i class="fas fa-users"></i> CRM',
				),
			);
			$menu['page'] = array(
				array(
					'link' => '/settings',
					'html' => '<i class="fas fa-cogs"></i>'
				),
				array(
					'link' => '/auth/shut',
					'html' => '<i class="fas fa-power-off"></i>',
				)
			);
		}

		$this->_container->view['menu'] = $menu;

		$RES = $NMW($REQ, $RES);

		return $RES;

	}

}
