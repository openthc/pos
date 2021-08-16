{% extends "layout/html.html" %}
{% import 'macros.html' as macros %}

{% block body %}

<div class="row mt-4">
<div class="col">
<div class="container">

<h1>Messaging</h1>

<div class="row">
<div class="col">
	{{ macros.bs_card("Text Blast", "Send a mass text to all of your customers, or filter the customer list.", '<a class="btn btn-outline-primary" href="/crm/message/sms"><i class="far fa-comments"></i> Send Texts</a>') }}
</div>
<div class="col">
	{{ macros.bs_card("Email Blast", "Mass emails, for monthly specials or large announcments.", '<a class="btn btn-outline-primary" href="/crm/message/email"><i class="fas fa-envelope-open-text"></i> Send Emails</a>') }}
</div>
</div>

</div>
</div>
</div>

{% endblock %}
