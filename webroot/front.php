<?php
/**
 * Front Controller
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
 */

use Edoceo\Radix\DB\SQL;

require_once(dirname(dirname(__FILE__)) . '/boot.php');

// Slim Application
$cfg = [];
// $cfg['debug'] = true;
$app = new \OpenTHC\App($cfg);


// Container
$con = $app->getContainer();
$con['DB'] = function($c) {

	$dbc = null;

	if (!empty($_SESSION['dsn'])) {
		$dbc = new SQL($_SESSION['dsn']);
	} else {
		$cfg = \OpenTHC\Config::get('database/main');
		$c = sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']);
		$u = $cfg['username'];
		$p = $cfg['password'];
		$dbc = new SQL($c, $u, $p);
	}

	return $dbc;

};


// Redis Connection
$con['Redis'] = function($c) {
	$r = new \Redis();
	$r->connect('127.0.0.1');
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
$app->group('/api', 'App\Module\API');


// Main Page
$app->group('/home', 'App\Module\Home')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// POS / Register
$app->group('/pos', 'App\Module\POS')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// CRM / Loyalty
$app->group('/crm', 'App\Module\CRM')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// B2B Operations
//$app->group('/report', 'App\Module\Report')
//	->add('App\Middleware\Menu')
//	->add('OpenTHC\Middleware\Session');


// Onsite & Online menus
$app->group('/menu', 'App\Module\Menu')
	->add('OpenTHC\Middleware\Session');


// Settings Interface
$app->group('/settings', 'App\Module\Settings')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Supplier
// $app->group('/supplier', 'App\Module\Supplier')
// 	->add('App\Middleware\Menu')
// 	->add('OpenTHC\Middleware\Session');

// B2B Operations
//$app->group('/b2b', 'App\Module\B2B')
//	->add('App\Middleware\Menu')
//	//->add('App\Middleware\Auth')
//	->add('OpenTHC\Middleware\Session');


// Authentication
$app->group('/auth', function() {
	$this->get('/open', 'App\Controller\Auth\oAuth2\Open');
	$this->get('/back', 'App\Controller\Auth\oAuth2\Back');
	$this->get('/init', 'App\Controller\Auth\Init')->setName('auth/init');
	$this->get('/connect', 'App\Controller\Auth\Connect'); // would like to merge with Open or Back
	$this->get('/ping', 'OpenTHC\Controller\Auth\Ping');
	$this->get('/shut', 'OpenTHC\Controller\Auth\Shut');
})
	->add('OpenTHC\Middleware\Session');


// Custom Middleware?
$f = sprintf('%s/Custom/boot.php', APP_ROOT);
if (is_file($f)) {
	// require_once($f);
}


// Execute
$app->run();

exit(0);
