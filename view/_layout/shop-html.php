<?php
/**
 * OpenTHC HTML Layout
 */

use Edoceo\Radix;
use Edoceo\Radix\Layout;
use Edoceo\Radix\Session;

header('content-type: text/html; charset=utf-8', true);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#069420">
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css" integrity="sha256-CTSx/A06dm1B063156EVh15m6Y67pAjZZaQc89LLSrU=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="/vendor/bootstrap/bootstrap.min.css" integrity="sha256-MBffSnbbXwHCuZtgPYiwMQbfE7z+GOZ7fBPCNB06Z98=" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="/css/main.css">
<!-- <link rel="stylesheet" href="/css/shop.css"> -->
<title><?= h(strip_tags($this->data['Page']['title'])) ?></title>
<head>
</head>
<body>
<?= $this->block('session-flash.php') ?>
<div class="container-fluid" style="min-height:80vh;">
<?= $this->body ?>
</div>

<script src="/vendor/lodash/lodash.min.js" integrity="sha256-qXBd/EfAdjOA2FGrGAG+b3YBn2tn5A6bhz+LSgYD96k=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/vendor/jquery/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/vendor/bootstrap/bootstrap.bundle.min.js" integrity="sha256-gvZPYrsDwbwYJLD5yeBfcNujPhRoGOY831wwbIzz3t0=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- <script src="https://app.openthc.com/js/app.js"></script> -->
<!-- <script src="/js/pos.js"></script> -->
<?= $this->foot_script ?>
</body>
</html>
