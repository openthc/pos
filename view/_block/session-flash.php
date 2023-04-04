<?php
/**
 * Output some Session Flash information
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use Edoceo\Radix\Session;

$x = Session::flash();
if ($x) {
	$x = str_replace('class="fail"', 'class="alert alert-danger"', $x);
	$x = str_replace('class="warn"', 'class="alert alert-warning"', $x);
	$x = str_replace('class="info"', 'class="alert alert-info"', $x);
	printf('<div class="container"><div class="alert-wrap mt-4">%s</div></div>', $x);
}
