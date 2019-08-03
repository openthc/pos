<?php
/**
 * OpenTHC Retail Front Controller
 */

use Edoceo\Radix\DB\SQL;

require_once(dirname(dirname(__FILE__)) . '/boot.php');

$cfg = array('debug' => true);
$app = new \OpenTHC\App($cfg);

// Container
$con = $app->getContainer();
$con['DB'] = function($c) {
	$cfg = \OpenTHC\Config::get('database_main');
	$c = sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']);
	$u = $cfg['username'];
	$p = $cfg['password'];
	$dbc = new SQL($c, $u, $p);
	return $dbc;
};


// Main Page
$app->get('/dashboard', 'App\Controller\Dashboard')
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


// Onsite & Online menus
$app->group('/menu', 'App\Module\Menu')
	->add('OpenTHC\Middleware\Session');


$app->group('/transfer', 'App\Module\Transfer')
	->add('App\Middleware\Menu')
	//->add('App\Middleware\Auth')
	->add('OpenTHC\Middleware\Session');


// Manage Interface
$app->group('/manage', function() {

	$this->get('', function($REQ, $RES, $ARG) {
		$data = array();
		$this->view->render($RES, 'page/manage/index.html', $data);
	});

	// Reports
	$this->group('/report', 'App\Module\Report');

})
->add('App\Middleware\Menu')
->add('OpenTHC\Middleware\Session');


// Retailer
//$app->group('/retailer', function() {
//
//	$this->get('', function($REQ, $RES, $ARG) {
//		$data = array();
//		$this->view->render($RES, 'page/retailer/empty.html', $data);
//	});
//
//	$this->get('/calendar', 'App\Controller\Retailer\Calendar');
//
//	$this->get('/inventory', 'App\Controller\Retailer\Inventory');
//	$this->post('/inventory', 'App\Controller\Retailer\Inventory');
//
//	$this->get('/reorder', 'App\Controller\Retailer\Reorder');
//
//	$this->get('/samples', 'App\Controller\Retailer\Samples');
//
//})
//->add('App\Middleware\Session');


// Supplier
$app->group('/supplier', 'App\Module\Supplier')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Authentication
$app->group('/auth', function() {

	$this->get('/open', 'App\Controller\Auth\oAuth2\Open');
	$this->get('/back', 'App\Controller\Auth\oAuth2\Back');
	$this->get('/fail', 'OpenTHC\Controller\Auth\Fail');
	$this->get('/ping', 'OpenTHC\Controller\Auth\Ping');
	$this->get('/shut', 'OpenTHC\Controller\Auth\Shut');

})
->add('OpenTHC\Middleware\Session');

$app->group('/api', 'App\Module\API');

$app->run();
