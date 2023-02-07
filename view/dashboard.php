<?php
/**
 * POS Dashboard
 *
 * SPDX-License-Identifier: MIT
 */

?>

<h1><?= __h($_SESSION['License']['name']) ?> :: <code><?= __h($_SESSION['License']['code']) ?></code></h1>

<!-- Icon Cards-->
<div class="row">
	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border-primary h-100">
		<div class="card-body">
			<div class="card-body-icon">
			<i class="fas fa-truck"></i> Retail Sales
			</div>
		</div>
		<a class="card-footer text-white bg-primary small z-1" href="/pos">
			<span class="float-left">View Details</span>
			<span class="float-right">
			<i class="fas fa-angle-right"></i>
			</span>
		</a>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border-warning h-100">
		<div class="card-body">
			<div class="card-body-icon">
			<i class="fas fa-fw fa-shopping-cart"></i> Delivery
			</div>
			<p>Delivery Management</p>
		</div>
		<a class="card-footer text-white bg-warning small z-1" href="/pos/delivery">
			<span class="float-left">View Details</span>
			<span class="float-right">
			<i class="fas fa-angle-right"></i>
			</span>
		</a>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border-success h-100">
		<div class="card-body">
			<div class="card-body-icon">
			<i class="fas fa-fw fa-life-ring"></i> On-Line
			</div>
			<p>Online Sales</p>
		</div>
		<a class="card-footer text-white bg-success small z-1" href="/pos/online">
			<span class="float-left">View Details</span>
			<span class="float-right">
			<i class="fas fa-angle-right"></i>
			</span>
		</a>
		</div>
	</div>
</div>
