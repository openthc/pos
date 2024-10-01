<?php
/**
 * Example OpenTHC POS Configuration
 */

$cfg = [];

// Database
$cfg['database'] = [
	'auth' => [
		'hostname' => 'localhost',
		'username' => 'openthc_auth',
		'password' => 'openthc_auth',
		'database' => 'openthc_auth',
	],
	'main' => [
		'hostname' => 'localhost',
		'username' => 'openthc_main',
		'password' => 'openthc_main',
		'database' => 'openthc_main',
	]
];

// Redis
$cfg['redis'] = [
	'hostname' => '127.0.0.1',
];

// Statsd
$cfg['statsd'] = [
	'hostname' => '127.0.0.1',
];

// OpenTHC Services
$cfg['openthc'] = [
	'app' => [
		'origin' => 'https://app.openthc.example',
		'public' => '/* APP SERVICE PUBLIC KEY */',
	],
	'b2b' => [
		'origin' => 'https://b2b.openthc.example',
		'public' => 'x',
		'secret' => 'x',
	],
	'dir' => [
		'origin' => 'https://dir.openthc.example',
		'public' => '/* DIR SERVICE PUBLIC KEY */',
	],
	'pipe' => [
		'origin' => 'https://pipe.openthc.example',
		'public' => '/* PIPE SERVICE PUBLIC KEY */',
	],
	'pos' => [
		'id' => '/* POS SERVICE ULID */',
		'origin' => 'https://pos.openthc.example',
		'public' => '/* POS SERVICE PUBLIC KEY */',
		'secret' => '/* POS SERVICE SECRET KEY */',
	],
	'sso' => [
		'origin' => 'https://sso.openthc.example',
		'public' => '/* SSO SERVICE PUBLIC KEY */',
		'client-id' => '/* POS SERVICE ULID */',
		'client-sk' => '/* POS SERVICE SSO CLIENT SECRET KEY */'
		// 'scope' => 'contact company profile cre pos',
	]
];

// Google
$cfg['google'] = [
	'api_key_js' => '',
	'map_api_key_js' => '',
];

return $cfg;
