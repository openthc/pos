<?php
/**
 * OpenTHC POS Application Bootstrap
 */

define('APP_NAME', getenv('APP_NAME') ?: 'OpenTHC | POS');
define('APP_HOST', getenv('APP_HOST') ?: 'pos.openthc.dev');
define('APP_SITE', 'https://' . APP_HOST);
define('APP_ROOT', __DIR__);
define('APP_SALT', ''); // put 16 to 32 characters here

openlog('openthc-pos', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

error_reporting(E_ALL & ~ E_NOTICE);

// Composer
require_once(APP_ROOT . '/vendor/autoload.php');

\OpenTHC\Config::init(APP_ROOT);

/**
 * Database Connection
 */
function _dbc($dsn=null)
{
	static $dbc_list = [];

	if (empty($dsn)) {
		$dsn = 'auth';
	}

	$dbc = $dbc_list[$dsn];
	if (!empty($dbc)) {
		return $dbc;
	}

	switch ($dsn) {
	case 'auth':
	case 'main':
	case 'root':

		$cfg = \OpenTHC\Config::get(sprintf('database/%s', $dsn));
		if (empty($cfg)) {
			_exit_text('Invalid Database Configuration [ABD-039]', 500);
		}

		$dbc_list[$dsn] = new \Edoceo\Radix\DB\SQL(sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']), $cfg['username'], $cfg['password']);

		return $dbc_list[$dsn];

	default:

		$dbc_list[$dsn] = new \Edoceo\Radix\DB\SQL($dsn);

		return $dbc_list[$dsn];

	}

	return null;
}

/**
 * You can put custom stuff here, it will be available to the entire application
 */
