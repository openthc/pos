{% extends "layout/html-pos.html" %}

{% block body %}

<style>
#delivery-map {
	background-color: #33333333;
	height: 100%;
	min-height: 480px;
	min-width: 320px;
	width: 100%;
}
</style>

<div class="container-fluid">

<h1>POS Delivery </h1>

<div class="row">
	<div class="col-md-6">
		<div class="table-responsive">
			<table class="table">
				<thead class="thead-dark">
					<tr>
					<th>Order</th>
					<th>Entered</th>
					<th>Total</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<div id="delivery-map">
			<h2>Map Loading...</h2>
		</div>
	</div>
</div>

</div>

{% endblock %}

{% block foot_script %}
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVJvvpHqOFkGNKknxBsE4VuVEsdSXGJbs"></script>
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

{% endblock %}
