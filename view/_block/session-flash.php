<?php
/**
 *
 */

use Edoceo\Radix\Session;

$x = Session::flash();
if ($x) {
	$x = str_replace('class="fail"', 'class="alert alert-danger"', $x);
	$x = str_replace('class="warn"', 'class="alert alert-warning"', $x);
	$x = str_replace('class="info"', 'class="alert alert-info"', $x);
	printf('<div class="container"><div class="alert-wrap">%s</div></div>', $x);
}
