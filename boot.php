<?php
/**
 * OpenTHC POS Application Bootstrap
 */

define('APP_NAME', getenv('APP_NAME') ?: 'OpenTHC | POS');
define('APP_HOST', getenv('APP_HOST') ?: 'pos.openthc.dev');
define('APP_SITE', 'https://' . APP_HOST);
define('APP_ROOT', __DIR__);

openlog('openthc-pos', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

error_reporting(E_ALL & ~ E_NOTICE);

// Composer
require_once(APP_ROOT . '/vendor/autoload.php');

/**
 * You can put custom stuff here, it will be available to the entire application
 */
