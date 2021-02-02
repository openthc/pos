{#
	Show List of Pending Inbound Transfer
#}

{% extends "layout/html.html" %}

{% block body %}

<h1>Transfer :: {{ Transfer.global_id }} <small>[{{ Transfer.manifest_type }} / {{ Transfer.status }}]</small></h1>

<div class="row">
<div class="col">
	<div class="form-group">
		<label>From:</label>
		<div class="input-group">
			<input class="form-control" readonly value="{{ Origin_License.name }} #{{ Origin_License.code }}">
			<div class="input-group-append"><a class="btn btn-outline-secondary" href="https://directory.openthc.com/company?id={{ Origin_License.company_id }}" target="_blank"><i class="fas fa-address-book"></i></a></div>
		</div>
		<small>{{ Transfer.global_from_mme_id }}</small>
	</div>
</div>
<div class="col">
	<div class="form-group">
		<label>Ship To:</label>
		<div class="input-group">
			<input class="form-control" readonly value="{{ Target_License.name }} #{{ Target_License.code }}">
			<div class="input-group-append"><a class="btn btn-outline-secondary" href="https://directory.openthc.com/company?id={{ Demand_License.company_id }}" target="_blank"><i class="fas fa-address-book"></i></a></div>
		</div>
		<small>{{ Transfer.global_to_mme_id }}</small>
	</div>
</div>
</div>

<hr>

<h2>Transfer Items:</h2>
<form autocomplete="off" method="post">
<table class="table">
<thead class="thead-dark">
	<tr>
		<th>ID</th>
		<th>Strain</th>
		<th>Description</th>
		<th class="r">Sent</th>
		<th class="r">Received</th>
		<th class="r">Price</th>
	</tr>
</thead>
<tbody>
{% for iti in Transfer.inventory_transfer_items %}
	<tr>
		<td>
			{{ iti.global_inventory_id }}<br>
			<small>txn: {{ iti.global_id }}</small>
		</td>
		<td>{{ iti.strain_name }}</td>
		<td>
			{{ iti.description }}<br>
			<small>
				{{ iti.retest ? "RETEST" }}
				{% if iti.is_sample %}
					{% if iti.sample_type == "lab_sample" %}
						Sample /
					{% endif %}
				{% endif %}
				{{ iti.inventory_type.type }} / {{ iti.inventory_type.intermediate_type }}
				{{ iti.is_for_extraction ? " / For Extract" }}
			</small>
		</td>
		<td class="r">{{ iti.qty }}</td>
		<!-- <td><pre>{{ dump(iti) }}</pre></td> -->
		<td>
			<input name="lot-receive-guid-{{ iti.global_id }}" type="hidden" value="{{ iti.global_id }}">
			<input class="form-control form-control-sm r" name="lot-receive-count-{{ iti.global_id }}" value="{{ iti.received_qty ?: iti.qty }}">
		</td>
		<td class="r">
			<input class="form-control r" readonly value="{{ iti.price }}">
		</td>
	</tr>
{% endfor %}
</tbody>
</table>

<div class="form-group">
	<label>Receive to:</label>
	<select class="form-control" name="zone-id">
	{% for Z in Zone_list %}
		<option value="{{ Z.guid }}">{{ Z.name }}</option>
	{% endfor %}
	</select>
</div>


<div class="form-actions">
	<button class="btn btn-outline-success btn-transfer-accept" disabled><i class="fas fa-check-square"></i> Accept</button>
	<button class="btn btn-outline-danger btn-transfer-accept" disabled><i class="fas fa-ban"></i> Void</button>
</div>

</form>

{% endblock %}

{% block foot_script %}
{{ parent() }}
<script>
$(function() {
	if ('in-transit' == '{{ Transfer.status }}') {
		$('.btn-transfer-accept').removeAttr('disabled');
	}
});
</script>
{% endblock %}
