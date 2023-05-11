<?php
/**
 * OpenTHC POS Example Configuration
 */

$cfg['database'] = [
	'auth' => [
		'hostname' => 'sql0',
		'username' => 'openthc_auth',
		'password' => 'openthc_auth',
		'database' => 'openthc_auth',
	],
	'main' => [
		'hostname' => 'sql0',
		'username' => 'openthc_main',
		'password' => 'openthc_main',
		'database' => 'openthc_main',
	],
];

$cfg['redis'] = [
	'hostname' => '127.0.0.1',
];

$cfg['statsd'] = [
	'hostname' => '127.0.0.1',
];

$cfg['openthc'] = [
	'app' => [
		'origin' => 'https://pos.openthc.example.com',
		'public' => 'pos.openthc.example.com',
		'secret' => 'SK/pos.openthc.example.com',
		// 'scope' => 'contact company',
	],
	'dir' => [
		'origin' => 'https://dir.openthc.example.com'
	],
	'pipe' => [
		'origin' => 'https://pipe.openthc.example.com',
		// 'scope' => 'pipe cre',
	],
	'sso' => [
		'origin' => 'https://sso.openthc.example.com',
		'secret' => 'SK/pos.openthc.example.com',
		// 'scope' => 'contact company profile cre pos',
	]
];

// Google
$cfg['google'] = [
	'api_key_js' => '',
	'map_api_key_js' => '',
];

return $cfg;
