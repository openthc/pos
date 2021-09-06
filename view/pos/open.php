
<style>
.numpad-wrap {
	display: flex;
	flex-wrap: wrap;
}
.numpad-grid {
	flex: 0 0 33.333333%;
	font-size: 32px;
}
.numpad-grid .btn {
	font-size: 32px;
	height: 4em;
	width: 100%;
}
</style>

<form autocomplete="off" method="post">
<input name="CSRF" type="hidden" value="<?= $data['CSRF'] ?>">

<div class="container mt-4">
	<div class="numpad-wrap">
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">1</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">2</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">3</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">4</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">5</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">6</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">7</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">8</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">9</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-secondary btn-action" type="button"><i class="fas fa-arrow-left"></i></button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-number" type="button">0</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-success btn-action" name="a" type="submit" value="auth-code"><i class="fas fa-arrow-right"></i></button></div>
	</div>

	<input class="form-control" id="auth-code" name="code" type="hidden">

</div>
</form>


<script>
var code_list = [];

$(function() {

	$('.btn-number').on('click', function() {
		var action = this.getAttribute('data-action');
		switch (action) {
		case 'back':
			code_list.pop();
			break;
		case 'next':
			break;
		}
	});

	$('.btn-number').on('click', function() {

		var n = parseInt(this.textContent, 10);
		code_list.push(n);

		$('#auth-code').val( code_list.join('+') );
	});
});
</script>
