<?php
/**
 * Show List of Pending Inbound Transfer
 */
?>

<div style="position:relative;">
<form action="/b2b/sync" autocomplete="off" method="post">
	<div class="btn-group" style="position:absolute; right: 0.25em; top: 0.25em;">
		<a class="btn btn-outline-primary" href="/b2b/incoming/create"><i class="fas fa-plus"></i> Create</a>
		<button class="btn btn-outline-secondary"><i class="fas fa-sync"></i></button>
	</div>
</form>
</div>

<h1>Transfers</h1>
<p>These are pending inbound transfers, the material should be accepted and processed</p>

<div class="table-responsive">
<table class="table table-sm">
<thead class="thead-dark">
	<tr>
		<th>Transfer</th>
		<th>Date</th>
		<th>From</th>
		<th>Ship To</th>
		<th>Type</th>
		<th>Status</th>
		<th></th>
	</tr>
</thead>
<tbody>
{% for t in transfer_list %}

	<tr>
	<td>{{ t.status_void ? "VOID/" }}<a href="/b2b/{{ t.id }}">{{ t.id }}</a></td>
	<td>{{ t.date }}</td>
	<td>
		{{ t.origin_license.name }}
	</td>
	<td>
		{{ t.target_license.name }}
	</td>
	<td>{{ t.meta.status_void ? "VOID/" }}{{ t.meta.manifest_type }}</td>
	<td>{{ t.meta.status }}</td>
	<td class="r">
		{% if "in-transit" ==  t.meta.status %}
			<a class="btn btn-sm btn-outline-primary" href="/b2b/{{ t.id }}/accept"><i class="fas fa-arrow-right"></i> Accept</a>
		{% endif %}
	</td>
	</tr>

{% endfor %}
</tbody>
</table>
</div>
