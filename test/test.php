#!/usr/bin/php
<?php
/**
 * OpenTHC App Test
 */

require_once(dirname(__DIR__) . '/boot.php');

// $arg = \OpenTHC\Docopt::parse($doc, ?$argv=[]);
// Parse CLI
$doc = <<<DOC
OpenTHC App Test

Usage:
	test <command> [options]
	test phpunit
	test phpstan
	test phplint

Options:
	--phpunit-filter=<FILTER>   Some Filter for PHPUnit
	--phpunit-filter=<FILTER>   Some Filter for PHPUnit
DOC;

$res = Docopt::handle($doc, [
	'exit' => false,
	'help' => true,
	'optionsFirst' => false,
]);
$cli_args = $res->args;
// if (empty($cli_args)) {
// 	echo $res->output;
// 	echo "\n";
// 	exit(1);
// }
// var_dump($cli_args);

define('OPENTHC_TEST_OUTPUT_BASE', \OpenTHC\Test\Helper::output_path_init());


// Call Linter?
$tc = new \OpenTHC\Test\Facade\PHPLint([
	'output' => OPENTHC_TEST_OUTPUT_BASE
]);
// $res = $tc->execute();
// var_dump($res);

#
# PHP-CPD
# vendor/openthc/common/test/phpcpd.sh
// vendor/bin/phpmd boot.php,webroot/main.php,lib/,test/ \
// 	html \
// 	cleancode \
// 	--report-file "${OUTPUT_BASE}/phpmd.html" \
// 	|| true

// Call PHPCS?
// $tc = \OpenTHC\Test\PHPStyle::execute();


// PHPStan
$tc = new OpenTHC\Test\Facade\PHPStan([
	'output' => OPENTHC_TEST_OUTPUT_BASE
]);
// $res = $tc->execute();
// var_dump($res);


// Psalm/Psalter?


// PHPUnit
// $cfg = [];
// $tc = new OpenTHC\Test\Facade\PHPUnit($cfg);
// $res = $tc->execute();
// var_dump($res);

chdir(sprintf('%s/test', APP_ROOT));

$arg = [];
$arg[] = 'phpunit';
$arg[] = '--configuration';
if (is_file(sprintf('%s/test/phpunit.xml', APP_ROOT))) {
	$arg[] = sprintf('%s/test/phpunit.xml', APP_ROOT);
} else {
	echo "!! Using phpunit.xml.dist\n";
	$arg[] = sprintf('%s/test/phpunit.xml.dist', APP_ROOT);
}
// $arg[] = '--coverage-xml';
// $arg[] = sprintf('%s/coverage', OPENTHC_TEST_OUTPUT_BASE);
$arg[] = '--log-junit';
$arg[] = sprintf('%s/phpunit.xml', OPENTHC_TEST_OUTPUT_BASE);
$arg[] = '--testdox-html';
$arg[] = sprintf('%s/testdox.html', OPENTHC_TEST_OUTPUT_BASE);
$arg[] = '--testdox-text';
$arg[] = sprintf('%s/testdox.txt', OPENTHC_TEST_OUTPUT_BASE);
$arg[] = '--testdox-xml';
$arg[] = sprintf('%s/testdox.xml', OPENTHC_TEST_OUTPUT_BASE);
// // Filter?
if ( ! empty($cli_args['--filter'])) {
	$arg[] = '--filter';
	$arg[] = $cli_args['--filter'];
}

ob_start();
$cmd = new \PHPUnit\TextUI\Command();
$res = $cmd->run($arg, false);
// var_dump($res);
// 0 == success
// 1 == ?
// 2 == Errors
$data = ob_get_clean();
switch ($res) {
case 0:
	$data.= "\nTEST SUCCESS\n";
	break;
case 1:
	$data.= "\nTEST FAILURE\n";
	break;
case 2:
	$data.= "\nTEST FAILURE (ERRORS)\n";
	break;
default:
	$data.= "\nTEST UNKNOWN ($res)\n";
	break;
}
$file = sprintf('%s/phpunit.txt', OPENTHC_TEST_OUTPUT_BASE);
file_put_contents($file, $data);

// PHPUnit Transform
$source = sprintf('%s/phpunit.xml', OPENTHC_TEST_OUTPUT_BASE);
$output = sprintf('%s/phpunit.html', OPENTHC_TEST_OUTPUT_BASE);
\OpenTHC\Test\Helper::xsl_transform($source, $output);


// Done
\OpenTHC\Test\Helper::index_create($html);


// Output Information
$origin = \OpenTHC\Config::get('openthc/app/origin');
$output = str_replace(sprintf('%s/webroot/', APP_ROOT), '', OPENTHC_TEST_OUTPUT_BASE);

echo "TEST COMPLETE\n  $origin/$output\n";
