{% extends "layout/html.html" %}

{% block body %}

<div class="container">

	<div class="hero">
		<h1>Receipt Text</h1>
	</div>

<div class="row">
	<div class="col-md-6">
		<h2>Editor</h2>

		<div class="form-group">
			<label>Header</label>
			<input class="form-control" name="pos-receipt-head">
			Maybe allow an image?
		</div>

		<textarea class="form-control" name="pos-receipt-tail" style="height: 40vh;">
THANKS FOR SHOPPING AT $name

1234 Fake St #145
Big City, ST, 12345
store@cannabis.example.com
http://domain.example.com/
+1 (555) 555-5555
		</textarea>
		<textarea class="form-control" name="pos-receipt-foot" style="height: 40vh;">
		</textarea>
	</div>
	<div class="col-md-6">
		<h2>Preview</h2>
		<img src="">
	</div>
</div>


</div>


{% endblock %}