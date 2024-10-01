<?php
/**
 * Main Controller
 *
 * This file is part of OpenTHC POS
 *
 * OpenTHC POS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * OpenTHC POS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenTHC POS.  If not, see <https://www.gnu.org/licenses/>.
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

require_once(dirname(dirname(__FILE__)) . '/boot.php');

// Slim Application
$cfg = [];
$cfg['debug'] = true;
$app = new \OpenTHC\App($cfg);


// Container
$con = $app->getContainer();
$con['DB'] = function($c) {

	$dbc = null;

	if (!empty($_SESSION['dsn'])) {
		$dbc = _dbc($_SESSION['dsn']);
	}

	return $dbc;

};


// Redis Connection
$con['Redis'] = function($c) {
	$x = \OpenTHC\Config::get('redis/hostname');
	$r = new \Redis();
	$r->connect($x);
	return $r;
};


// Get Current Company Object
$con['Company'] = function($c0) {

	static $C;

	if (empty($C)) {

		$dbc = $c0->DB;
		$C = new \OpenTHC\Company($dbc, $_SESSION['Company']);

	}

	return $C;

};


// API
$app->group('/api/v2018', 'OpenTHC\POS\Module\API');


// Main Page
$app->group('/dashboard', 'OpenTHC\POS\Module\Dashboard')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// POS / Register
$app->group('/pos', 'OpenTHC\POS\Module\POS')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// CRM / Loyalty
$app->group('/crm', 'OpenTHC\POS\Module\CRM')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// B2B Operations
$app->group('/report', 'OpenTHC\POS\Module\Report')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// Onsite & Online menus
$app->group('/menu', 'OpenTHC\POS\Module\Menu')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// External Shop
$app->group('/shop', 'OpenTHC\POS\Module\Shop')
	->add('OpenTHC\Middleware\Session');


// CRM / Loyalty
$app->get('/contact/ajax', 'OpenTHC\POS\Controller\Contact')
	->add('OpenTHC\POS\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');

// Vendor
// $app->group('/vendor', 'OpenTHC\POS\Module\Vendor')
// 	->add('OpenTHC\POS\Middleware\Auth')
// 	->add('OpenTHC\Middleware\Session');


// Authentication
$app->group('/auth', function() {
	$this->get('/open', 'OpenTHC\POS\Controller\Auth\oAuth2\Open');
	$this->get('/back', 'OpenTHC\POS\Controller\Auth\oAuth2\Back');
	$this->get('/init', 'OpenTHC\POS\Controller\Auth\Init')->setName('auth/init');
	$this->get('/connect', 'OpenTHC\POS\Controller\Auth\Connect'); // would like to merge with Open or Back
	$this->get('/ping', 'OpenTHC\Controller\Auth\Ping');
	$this->get('/shut', 'OpenTHC\POS\Controller\Auth\Shut');
})
	->add('OpenTHC\Middleware\Session');


// Intent
$app->map(['GET','POST'], '/intent', 'OpenTHC\POS\Controller\Intent')
	->add('OpenTHC\Middleware\Session');


// Webhooks
$app->group('/webhook', function() {

	$this->post('/weedmaps/order', function($REQ, $RES, $ARG) {

		$file = sprintf('%s/var/weedmaps-order-%s.txt', APP_ROOT, \Edoceo\Radix\ULID::create());
		$json = file_get_contents('php://input');
		file_put_contents($file, json_encode([
			'_GET' => $_GET,
			'_POST' => $_POST,
			'_ENV' => $_ENV,
			'_BODY' => $json
		]));

		$data = json_decode($json, true);
		switch ($data['status']) {
			case 'DRAFT':
				__exit_json($data);
				break;
			case 'PENDING':
				__exit_json($data);
				break;
		}

		__exit_json([
			'data' => null,
			'meta' => [ 'detail' => 'Request Not Handled' ]
		], 400);

	});
});


// Custom Middleware?
$f = sprintf('%s/Custom/boot.php', APP_ROOT);
if (is_file($f)) {
	require_once($f);
}

// Execute
$app->run();

exit(0);
