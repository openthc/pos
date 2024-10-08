
<header style="display:flex; justify-content: space-between;">
	<div>
		<h1>POS Delivery </h1>
	</div>
	<div><?php
	$delivery_auth_link = sprintf('https://%s/intent?%s'
		, $_SERVER['SERVER_NAME']
		, http_build_query([
			'a' => 'delivery-auth',
			'c' => $_SESSION['Company']['id'],
			'l' => $_SESSION['License']['id']
		])
	);
	// printf('<button class="btn btn-outline-secondary qrcode-link" data-code="%s" type="button"><i class="fas fa-qrcode"></i> Courier Auth</button>'
	// 	, $delivery_auth_link
	// );

	$link = sprintf('/pos/delivery/ajax?%s'
		, http_build_query([
			'a' => 'delivery-auth',
			'c' => $_SESSION['Company']['id'],
			'l' => $_SESSION['License']['id']
		])
	);
	printf('<button class="btn btn-outline-secondary qrcode-link" data-load="%s" type="button"><i class="fas fa-qrcode"></i> Courier Auth</button>'
		, $link
	);

	?></div>
</header>



<div class="row">
	<div class="col-md-6">

		<h2>Active Couriers</h2>
		<form method="post">
		<table class="table">
				<thead class="thead-dark">
					<tr>
					<th>Courier</th>
					<th>Active</th>
					<th>Location</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($data['courier_list'] as $c) {
					$ping = sprintf('%d m ago', ceil($c['ping'] / 60));
					echo '<tr>';
					printf('<td>%s</td>', $c['name']);
					printf('<td>%s / %s</td>', $c['stat'], $ping);
					printf('<td>%s</td>', $c['location']);
					echo '</tr>';
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">
							<select class="form-select">
						<?php
						foreach ($data['contact_list'] as $c) {
							printf('<option value="%s">%s</option>', $c['id'], __h($c['name']));
						}
						?>
							</select>
						</td>
						<td class="r">
							<button class="btn btn-primary"><i class="fas fa-plus"></i></button>
						</td>
					</tr>
				</tfoot>
		</table>
		</form>

		<hr>

		<h2>Orders</h2>

		<div class="table-responsive">
			<table class="table">
				<thead class="thead-dark">
					<tr>
					<th>Order</th>
					<th>Courier</th>
					<th>Cart</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($data['b2c_sale_hold'] as $rec) {

					$b2c_item = __json_decode($rec['meta']);
					if (empty($b2c_item['item_list'])) {
						$b2c_item['item_list'] = [];
					}
					foreach ($b2c_item as $k => $v) {
						if (preg_match('/^qty-(\w+)$/', $k, $m)) {
							$b2c_item['item_list'][] = [
								'id' => $m[1],
							];
						}
					}

					echo '<tr>';
					printf('<td><a href="/pos#%s">%s</a></td>', $rec['id'], $rec['id']);
					printf('<td>%s</td>', $rec['contact_name']);
					printf('<td>%d Items</td>', count($b2c_item['item_list']));
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<div id="delivery-map" style="max-height: 800px;">
			<h2>Map Loading...</h2>
		</div>
	</div>
</div>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= $data['map_api_key_js'] ?>"></script>
<script>
var opt = {
	zoom: 11,
	zoomControlOptions:{
		style: google.maps.ZoomControlStyle.SMALL
	}
};
var div = document.getElementById('delivery-map');
$(function() {

	var cpt = new google.maps.LatLng(47.60, -122.25);
	var Map = new google.maps.Map(div, opt);
	Map.setCenter(cpt);

	// Draw Four Random Markers In This Area
	var opt_marker = {
		draggable: false,
		dragCrossMove: false,
		label: 'A',
		icon: {
			path: 'M 1 1 H 24 V 24 H 1 Z',
			fillColor: '#cc0000',
			fillOpacity: 0.40,
			strokeColor: '#333333',
			strokeOpacity: 0.80,
			// path: 'M4,0v-1.5a2,2,0,0,0,-2,-2h-4a2,2,0,0,0,-2,2v3a2,2,0,0,0,2,2h4a2,2,0,0,0,-0.8387096774193523,-2Z'
			// url: mark.marker.url,
			labelOrigin: new google.maps.Point(13, 13)
		}
	};
	opt_marker.position = new google.maps.LatLng(47.65, -122.25);
	var mk0 = new google.maps.Marker(opt_marker)
	mk0.setMap(Map);

	opt_marker.label = 'B';
	opt_marker.icon.fillColor = '#00cc00';
	opt_marker.position = new google.maps.LatLng(47.6012, -122.30);
	var mk1 = new google.maps.Marker(opt_marker)
	mk1.setMap(Map);

	opt_marker.label = 'C';
	opt_marker.icon.fillColor = '#0000cc';
	opt_marker.position = new google.maps.LatLng(47.55, -122.365);
	var mk2 = new google.maps.Marker(opt_marker)
	mk2.setMap(Map);

	opt_marker.label = 'D';
	opt_marker.icon.fillColor = '#cccc00';
	opt_marker.position = new google.maps.LatLng(47.68, -122.38);
	var mk3 = new google.maps.Marker(opt_marker)
	mk3.setMap(Map);

});
</script>
