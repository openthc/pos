#!/usr/bin/php
<?php
/**
 * Make Helper
 */

use OpenTHC\Make;

if ( ! is_file(__DIR__ . '/vendor/autoload.php')) {
	$cmd = [];
	$cmd[] = 'composer';
	$cmd[] = 'install';
	$cmd[] = '--classmap-authoritative';
	$cmd[] = '2>&1';
	echo "Composer:\n";
	passthru(implode(' ', $cmd), $ret);
	var_dump($ret);
}

require_once(__DIR__ . '/boot.php');

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

create_homepage();

/**
 *
 */
function create_homepage() {

	$cfg = \OpenTHC\Config::get('openthc/pos/origin');
	$url = sprintf('%s/home', $cfg);
	$req = _curl_init($url);
	$res = curl_exec($req);
	$inf = curl_getinfo($req);
	if (200 == $inf['http_code']) {
		$file = sprintf('%s/webroot/index.html', APP_ROOT);
		$data = $res;
		file_put_contents($file, $data);
	}

}


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
