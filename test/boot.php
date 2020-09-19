<?php
/**
 * PHP Unit Test Bootstrap
 */

// App Bootstrap File
require_once(dirname(dirname(__FILE__)) . '/boot.php');
require_once(__DIR__ . '/Base.php');

// require_once(__DIR__ . '/lib/UI_TestCase_BrowserStack.php');
// require_once(__DIR__ . '/lib/UI_TestCase_TestingBot.php');
require_once(__DIR__ . '/lib/UI_TestCase.php');

function _echo_as_json($x)
{
	ksort_r($x);
	echo json_encode($x, JSON_PRETTY_PRINT);
}
