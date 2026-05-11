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
			<div class="card-header fs-2">
				<i class="fa-solid fa-cash-register"></i> Retail Sales
			</div>
			<div class="card-body">
				<p>Open the Register/Terminal for in-store checkout.</p>
			</div>
			<div class="card-footer">
				<a class="btn btn-primary" href="/pos">
					Open Register <i class="fas fa-angle-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border-warning h-100">
			<div class="card-header fs-2">
				<i class="fa-solid fa-truck-fast"></i> Delivery
			</div>
			<div class="card-body">
				<p>Delivery Management</p>
			</div>
			<div class="card-footer">
				<a class="btn btn-warning disabled" disabled href="/pos/delivery">
					View Details <i class="fas fa-angle-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border h-100">
			<div class="card-header fs-2">
				<i class="fa-regular fa-cloud"></i> On-Line
			</div>
			<div class="card-body">
				<p>Online Sales</p>
			</div>
			<div class="card-footer">
				<a class="btn btn-secondary disabled" disabled href="/pos/online">
					View Details <i class="fas fa-angle-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border h-100">
			<div class="card-header fs-2">
				<i class="fa-solid fa-tablet-screen-button"></i> Kiosk
			</div>
			<div class="card-body">
				<p>Enter Kiosk Mode</p>
			</div>
			<div class="card-footer">
				<a class="btn btn-secondary disabled" disabled href="/kiosk">
					Launch Kiosk <i class="fas fa-angle-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="col-xl-4 col-sm-6 mb-3">
		<div class="card border h-100">
			<div class="card-header fs-2">
				<i class="fa-solid fa-gears"></i> Settings
			</div>
			<div class="card-body">
				<p>System Settings and Terminal Configuration</p>
			</div>
			<div class="card-footer">
				<a class="btn btn-secondary" href="/config">
					Update Settings <i class="fas fa-angle-right"></i>
				</a>
			</div>
		</div>
	</div>

</div>
