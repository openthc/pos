{% extends "layout/html.html" %}

{% block body %}

		<div class="container-fluid mt-2">

		  <!-- Breadcrumbs-->
		  <ol class="breadcrumb">
			<li class="breadcrumb-item">
			  <a href="#">Dashboard</a>
			</li>
			<li class="breadcrumb-item active">Overview</li>
		  </ol>

		  <!-- Icon Cards-->
		  <div class="row">
			<div class="col-xl-4 col-sm-6 mb-3">
			  <div class="card border-success o-hidden h-100">
				<div class="card-body">
				  <div class="card-body-icon">
					<i class="fas fa-truck"></i> Retail Sales
				  </div>
				  <p>Recently Completed Transactions</p>
				</div>
				<a class="card-footer text-white bg-success clearfix small z-1" href="/report/b2c/recent">
				  <span class="float-left">View Details</span>
				  <span class="float-right">
					<i class="fas fa-angle-right"></i>
				  </span>
				</a>
			  </div>
			</div>
			<!-- <div class="col-xl-3 col-sm-6 mb-3">
			  <div class="card border-warning o-hidden h-100">
				<div class="card-body">
				  <div class="card-body-icon">
					<i class="fas fa-fw fa-list"></i> Inventory
				  </div>
				  <p>Products and Inventory</p>
				</div>
				<a class="card-footer text-white bg-warning small z-1" href="/inventory">
				  <span class="float-left">View Details</span>
				  <span class="float-right">
					<i class="fas fa-angle-right"></i>
				  </span>
				</a>
			  </div>
			</div> -->
			<div class="col-xl-4 col-sm-6 mb-3">
			  <div class="card border-warning o-hidden h-100">
				<div class="card-body">
				  <div class="card-body-icon">
					<i class="fas fa-fw fa-shopping-cart"></i> Delivery
				  </div>
				  <p>Delivery Management</p>
				</div>
				<a class="card-footer text-white bg-warning clearfix small z-1" href="/pos/delivery">
				  <span class="float-left">View Details</span>
				  <span class="float-right">
					<i class="fas fa-angle-right"></i>
				  </span>
				</a>
			  </div>
			</div>
			<div class="col-xl-4 col-sm-6 mb-3">
			  <div class="card border-danger o-hidden h-100">
				<div class="card-body">
				  <div class="card-body-icon">
					<i class="fas fa-fw fa-life-ring"></i> On-Line
				  </div>
				  <p>Online Sales</p>
				</div>
				<a class="card-footer text-white bg-danger clearfix small z-1" href="/pos/online">
				  <span class="float-left">View Details</span>
				  <span class="float-right">
					<i class="fas fa-angle-right"></i>
				  </span>
				</a>
			  </div>
			</div>
		  </div>

		  <!-- Area Chart Example-->
		  <div class="row">
			  <div class="col">
				<div class="card mb-3">
				<div class="card-header">
				<i class="fas fa-chart-area"></i> Revenue :: Daily</div>
				<div class="card-body">
					<div id="chart-sales-daily-sum-wrap" style="height:240px;">
						<h1>Loading Chart...</h1>
					</div>
				</div>
				<div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
				</div>
			</div>
			<div class="col">
				<div class="card mb-3">
				<div class="card-header">
				<i class="fas fa-chart-area"></i> Revenue :: Product Type</div>
				<div class="card-body">
					<div id="chart-sales-product-type-wrap" style="height:240px;">
						<h1>Loading Chart...</h1>
					</div>
				</div>
				<div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
				</div>
			</div>
		</div>

		  <!-- DataTables Example -->
		  <div class="card mb-3">
			<div class="card-header"><i class="fas fa-table"></i> Most Recent Sales</div>
			<div class="card-body">
			  <div class="table-responsive" id="pos-sale-table-recent">
				<table class="table table-sm">
					<thead class="thead-dark">
						<tr>
							<th>Time</th>
							<th>Register</th>
							<th class="r">Items</th>
							<th class="r">Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="4"><i class="fas fa-sync fa-spin"></i> Loading...</td></tr>
					</tbody>
				</table>
			  </div>
			</div>
			<div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
		  </div>

		</div>
		<!-- /.container-fluid -->

{% endblock %}

{% block foot_script %}
{{ parent() }}

<script>
$(function() {

	$.get('/report/ajax/revenue-daily')
		.done(function(body) {
			$('#chart-sales-daily-sum-wrap').empty();
			$('#chart-sales-daily-sum-wrap').append('<canvas id="chart-sales-daily-sum" height="240" style="height:240px;"></canvas>');
			var ctx = document.getElementById('chart-sales-daily-sum').getContext('2d');
			window.Chart0 = new Chart(ctx, body);
		});

	$.get('/report/ajax/revenue-product-type')
		.done(function(body) {
			$('#chart-sales-product-type-wrap').empty();
			$('#chart-sales-product-type-wrap').append('<canvas id="chart-sales-product-type" height="240" style="height:240px;"></canvas>');
			var ctx = document.getElementById('chart-sales-product-type').getContext('2d');
			window.Chart1 = new Chart(ctx, body);
		});

	$.get('/home/ajax?a=sale-recent')
		.done(function(body) {
			$('#pos-sale-table-recent').empty();
			$('#pos-sale-table-recent').html(body);
		});

});
</script>
{% endblock %}
