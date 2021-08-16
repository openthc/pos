{#
	Modal for Loyalty Program
	@todo Maybe just one input box for any/all codes?
#}

{% extends "block/modal.html" %}

{% set modal_title = "Sales :: Loyalty" %}
{% set modal_id = "pos-modal-loyalty" %}

{% block body %}

<div class="row mb-4">
	<div class="col-md-6">
		<h5>Phone</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control" id="loyalty-phone" inputmode="tel" type="phone">
			<div class="input-group-append">
				<div class="input-group-text"><i class="fas fa-hashtag"></i></div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col-md-6">
		<h5>Email</h5>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control" id="loyalty-email" inputmode="email" type="email">
			<div class="input-group-append"><div class="input-group-text"><i class="fas fa-at"></i></div></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<h5>Code / ID</h5>
		<p>Their Loyalty ID or scanned ID card</p>
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<input autocomplete="off" class="form-control" id="loyalty-other" type="text">
			<div class="input-group-append">
				<button class="btn btn-secondary" type="button"><i class="fas fa-qrcode"></i></button>
			</div>
		</div>
	</div>
</div>

<!--
<div>
	<h3 class="pos-checkout-sum" style="text-align:right;"></h3>
	<h3 class="pos-checkout-sum-adj" style="text-align:right;">-</h3>
	<h3 class="pos-checkout-sum-new" style="text-align:right;">-</h3>
</div>

<div>
	<h2 style="background: #212121; color:#fcfcfc; margin:0; padding:4px;">Pre-Defined Discount Schedules</h2>
</div>

<div id="pos-modal-discount-list">
	<h3>Loading Discounts...</h3>
</div>
-->

{% endblock %}

{% block foot %}
<button class="btn btn-outline-primary" id="pos-loyalty-apply" name="a" type="submit" value="pos-discount-apply"><i class="fas fa-check-square"></i> Apply</button>
{% endblock %}
