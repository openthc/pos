<?php
/**
 * OpenTHC POS Application Bootstrap
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

define('APP_ROOT', __DIR__);
define('APP_SALT', ''); // put 16 to 32 characters here
define('APP_BUILD', '420.23.031');

openlog('openthc-pos', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

error_reporting(E_ALL & ~ E_NOTICE);

// Composer
require_once(APP_ROOT . '/vendor/autoload.php');

if ( ! \OpenTHC\Config::init(APP_ROOT) ) {
	_exit_html_fail('<h1>Invalid Application Configuration [POS-017]</h1>', 500);
}

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

		$dbc_list[$dsn] = new \Edoceo\Radix\DB\SQL(sprintf('pgsql:application_name=openthc-pos;host=%s;dbname=%s', $cfg['hostname'], $cfg['database']), $cfg['username'], $cfg['password']);

		return $dbc_list[$dsn];

	default:

		$dbc_list[$dsn] = new \Edoceo\Radix\DB\SQL($dsn);

		return $dbc_list[$dsn];

	}

	return null;
}

function _draw_html_card($head, $body, $foot=null)
{
	ob_start();
	echo '<div class="card">';
	printf('<div class="card-header">%s</div>', $head);
	printf('<div class="card-body">%s</div>', $body);
	if ($foot) {
		printf('<div class="card-footer">%s</div>', $foot);
	}
	echo '</div>';

	return ob_get_clean();
}

/**
 * You can put custom stuff here, it will be available to the entire application
 */
