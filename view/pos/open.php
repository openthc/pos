
<form autocomplete="off" method="post">
<input name="CSRF" type="hidden" value="<?= $data['CSRF'] ?>">

<div class="container mt-4">
	<div class="form-control form-control-lg" id="auth-code-view" style="font-size: 150%; font-weight: bold; letter-spacing: 1em; line-height:1; text-align: center;"></div>
	<input id="auth-code" name="code" type="hidden">
	<div class="numpad-wrap">
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="1">1</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="2">2</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="3">3</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="4">4</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="5">5</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="6">6</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="7">7</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="8">8</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="9">9</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-secondary btn-action" type="button" value="x"><i class="fas fa-arrow-left"></i></button></div>
		<div class="numpad-grid"><button class="btn btn-outline-primary btn-action" type="button" value="0">0</button></div>
		<div class="numpad-grid"><button class="btn btn-outline-success" name="a" type="submit" value="auth-code"><i class="fas fa-arrow-right"></i></button></div>
	</div>

</div>
</form>


<script>
var char_list = [
	'&alpha;'
	, '&beta;'
	, '&gamma;'
	, '&delta;'
	, '&epsilon;'
	, '&zeta;'
	, '&eta;'
	, '&theta;'
	, '&iota;'
	, '&kappa;'
	, '&lambda;'
	, '&mu;'
	, '&nu;'
	, '&xi;'
	, '&omicron;'
	, '&pi;'
	, '&rho;'
	, '&sigma;'
	, '&tau;'
	, '&upsilon;'
	, '&phi;'
	, '&chi;'
	, '&psi;'
	, '&omega;'
];

// Everyones Favourite Shuffle
// @see https://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
var idx = char_list.length - 1;
for (idx; idx > 0; idx--) {
	const rnd = Math.floor(Math.random() * (idx + 1));
	const tmp = char_list[idx];
	char_list[idx] = char_list[rnd];
	char_list[rnd] = tmp;
}

var code_list = [];
var code_mask = [];
var code_auth = document.querySelector('#auth-code');
var code_view = document.querySelector('#auth-code-view');

$(function() {

	$('.btn-action').on('click', function() {

		var x = this.value;
		if ('x' === this.value) {
			code_list.pop();
			code_mask.pop();
		} else {
			code_list.push(x);
			var c = char_list[ code_list.length - 1 ];
			code_mask.push(c);
		}

		code_auth.value = code_list.join('');
		code_view.innerHTML = code_mask.join('');

	});

});
</script>
