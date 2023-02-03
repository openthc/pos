<?php
/**
 * OpenTHC HTML Layout
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

header('content-type: text/html; charset=utf-8', true);

$body_class_list = [];
$m1_mode = preg_match('/^(open|mini|shut)$/', $_COOKIE['m1'], $m) ? $m[1] : 'open';
$body_class_list[] = sprintf('m1-%s', $m1_mode);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#069420">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha256-mUZM63G8m73Mcidfrv5E+Y61y7a12O5mW4ezU3bxqW4=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="https://cdn.openthc.com/jqueryui/1.12.1/jqueryui.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.openthc.com/bootstrap/5.1.3/bootstrap.min.css" integrity="sha256-YvdLHPgkqJ8DVUxjjnGVlMMJtNimJ6dYkowFFvp4kKs=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css" integrity="sha256-F+DaKAClQut87heMIC6oThARMuWne8+WzxIDT7jXuPA=" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.openthc.com/css/www/0.0.2/main.css" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="https://cdn.openthc.com/css/www/0.0.2/menu-tlr.css" crossorigin="anonymous"> -->
<link rel="stylesheet" href="/css/main.css">
<title><?= h(strip_tags($this->data['Page']['title'])) ?></title>
</head>
<body class="<?= implode(' ', $body_class_list) ?>" data-menu-left-mode="<?= $m1_mode ?>">
<?= $this->block('body-head.php') ?>
<?= $this->block('session-flash.php') ?>
<div class="container-fluid" style="min-height:80vh;">
<?= $this->body ?>
</div>

<script src="https://cdn.openthc.com/lodash/4.17.15/lodash.js" integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.openthc.com/jquery/3.4.1/jquery.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.openthc.com/bootstrap/5.1.3/bootstrap.bundle.min.js" integrity="sha256-9SEPo+fwJFpMUet/KACSwO+Z/dKMReF9q4zFhU/fT9M=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha256-xUHvBjJ4hahBW8qN9gceFBibSFUzbe9PNttUvehITzY=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js" integrity="sha256-yz7K02nILYEeRDwEfzu/1zI9SpBKod/nLYMTFh7vszs=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- <script src="https://app.openthc.com/js/app.js"></script> -->
<script src="/js/pos.js"></script>
<script>
$(function() {

	//$('#exec-full-screen').on('click', function() {
	//      var el = document.documentElement;
	//      var rfs = el.requestFullScreen
	//                      || el.webkitRequestFullScreen
	//                      || el.mozRequestFullScreen;
	//      rfs.call(el);
	//});

	// $('#pos-terminal-id').on('click', function() {
	// 	// Weed.bodyDim();
	// 	//$.get('/pos/ajax/tid', function() {
	// 	var html = '';
	// 	html+= '<div style="background: #fdfdfd; border:4px solid #000; height: 400px; margin: 0px auto; width: 400px;">';
	// 	// html+= '<img src="http://chart.apis.google.com/chart?cht=qr&chs=400x400&chl=<?= rawurlencode('https://weedtraqr.com/pos/front?t=' . $_SESSION['pos-terminal-id']) ?>&chld=H|0">';
	// 	html+= '</div>';
	// 	Weed.modal(html);
	// });

	// $('#pos-sign-out').on('click', function() {
	// 	window.location = '/auth/pin';
	// });

});
</script>
<?= $this->foot_script ?>
</body>
</html>
