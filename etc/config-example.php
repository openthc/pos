<?php
/**
 * OpenTHC POS Configuration Example
 */

// 'Client ID, Could be Public Key'
$my_client_id = '010PENTHCX0000SVC000000P0S'; // pos.openthc.example;
$my_client_pk = 'pos.openthc.example';
$my_client_sk = 'SK/pos.openthc.example';

$cfg = [];

// Database
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

// Redis
$cfg['redis'] = [
	'hostname' => '127.0.0.1',
];

// Statsd
$cfg['statsd'] = [
	'hostname' => '127.0.0.1',
];

// OpenTHC
$cfg['openthc'] = [
	'app' => [
		'origin' => 'https://app.openthc.example',
		'public' => 'App Public Key',
		'secret' => $my_client_sk,
		// 'scope' => 'contact company',
	],
	'dir' => [
		'origin' => 'https://dir.openthc.example',
		'public' => 'Directory Public Key',
		// 'secret' => '',
	],
	'pipe' => [
		'origin' => 'https://pipe.openthc.example',
		// 'scope' => 'pipe cre',
	],
	'pos' => [
		'id' => '/* POS Service ULID */',
		'origin' => 'https://pos.openthc.example',
	],
	'sso' => [
		'origin' => 'https://sso.openthc.example',
		'oauth-client-id' => '/* POS SERVICE ULID */',
		'oauth-client-sk' => '/* POS SERVICE Client Secret */'
		// 'scope' => 'contact company profile cre pos',
	]
];

// Google
$cfg['google'] = [
	'api_key_js' => '',
	'map_api_key_js' => '',
];

return $cfg;
