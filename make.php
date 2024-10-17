#!/usr/bin/php
<?php
/**
 * Make Helper
 */

use OpenTHC\Make;

define('APP_ROOT', __DIR__);

if ( ! is_file(APP_ROOT . '/vendor/autoload.php')) {
	$cmd = [];
	$cmd[] = 'composer';
	$cmd[] = 'install';
	$cmd[] = '--classmap-authoritative';
	$cmd[] = '2>&1';
	echo "Composer:\n";
	passthru(implode(' ', $cmd), $ret);
	var_dump($ret);
}

require_once(APP_ROOT . '/vendor/autoload.php');
chdir(APP_ROOT);

$doc = <<<DOC
OpenTHC Directory Make Helper

Usage:
	make [options]

Commands:
	install

Options:
	--filter=<FILTER>   Some Filter for PHPUnit
DOC;
// $cli_args

Make::composer();

Make::npm();

Make::install_bootstrap();

Make::install_fontawesome();

Make::install_jquery();

# lodash
$s = 'node_modules/lodash/lodash.min.js';
$t = 'webroot/vendor/lodash/lodash.min.js';
install_file($s, $t);

# qrcode
$s = 'node_modules/qrcodejs/qrcode.min.js';
$t = 'webroot/vendor/qrcodejs/qrcode.min.js';
install_file($s, $t);

# chart.js
$s = 'node_modules/chart.js/dist/chart.umd.js';
$t = 'webroot/vendor/chart.js/chart.umd.js';
install_file($s, $t);
// cp node_modules/chart.js/dist/chart.umd.js.map "$outpath/"

# echarts like from APP


#
# SASS
$cmd = [];
$cmd[] = './node_modules/.bin/sass';
$cmd[] = '--fatal-deprecation 1.65.0';
$cmd[] = '--no-charset';
$cmd[] = '--no-source-map';
$cmd[] = '--style=compressed';
$cmd[] = '--stop-on-error';
$cmd[] = 'sass/main.scss webroot/css/main.css';
$cmd[] = '2>&1';
$cmd = implode(' ', $cmd);
echo shell_exec($cmd);
echo "\n";

Make::create_homepage('pos');



function install_file(string $s, string $t) : bool
{
	if ( ! is_file($s)) {
		return false;
	}

	$p = dirname($t);
	if ( ! is_dir($p)) {
		mkdir($p, 0755, true);
	}

	copy($s, $t);

	return true;

}
