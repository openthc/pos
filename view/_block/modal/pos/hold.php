{#
	Modal for Saving the Current Sale
#}

{% extends "block/modal.html" %}

{% set modal_title = "Sales :: Create Hold" %}
{% set modal_id = "pos-modal-sale-hold" %}

{% block body %}

<div>
	<div class="form-group">
	<label>Customer Name/Note</label>
		<input autocomplete="off" class="form-control" id="customer-name" value="">
	</div>
</div>

{% endblock %}

{% block foot %}
<button class="btn btn-outline-primary" id="pos-modal-sale-hold-save" name="a" type="button" value="save"><i class="fas fa-save"></i> Save</button>
{% endblock %}
