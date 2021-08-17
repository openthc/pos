
<!-- Breadcrumbs-->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
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


<script>
$(function() {

	fetch('/dashboard/ajax?a=b2c-revenue-daily')
		.then(function(res) {
			// res.ok == true
			// res.status == 200
			// res.headers.get('content-type'); // strtok(';');
			return res.json();
		})
		.then(function(body) {
			var $wrap = $('#chart-sales-daily-sum-wrap');
			$wrap.empty();
			if ((body.data) && ('object' == typeof body.data)) {
				$wrap.append('<canvas id="chart-sales-daily-sum" height="240" style="height:240px;"></canvas>');
				var ctx = document.getElementById('chart-sales-daily-sum').getContext('2d');
				window.Chart0 = new Chart(ctx, body.data);
			} else {
				$wrap.append(`<div class="alert alert-warning">${body.meta.detail}</div>`);
			}
		});

	fetch('/dashboard/ajax?a=b2c-revenue-daily-product-type')
		.then(res => res.json())
		.then(function(body) {
			var $wrap = $('#chart-sales-product-type-wrap')
			$wrap.empty();
			if ((body.data) && (body.data.length)) {
				$wrap.append('<canvas id="chart-sales-product-type" height="240" style="height:240px;"></canvas>');
				var ctx = document.getElementById('chart-sales-product-type').getContext('2d');
				window.Chart1 = new Chart(ctx, body.data);
			} else {
				$wrap.append(`<div class="alert alert-warning">${body.meta.detail}</div>`);
			}
		});

	fetch('/dashboard/ajax?a=b2c-recent')
		.then(res => res.text())
		.then(function(body) {
			$('#pos-sale-table-recent').empty();
			$('#pos-sale-table-recent').html(body);
		});

});
</script>
