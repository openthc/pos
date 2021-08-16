
<style>
.inline-edit-wrap {

}
.inline-edit-wrap .inline-edit-edit {
	display:none;
}
.inline-edit-wrap .inline-edit-view {
	position: relative;
}
.inline-edit-wrap .inline-edit-view .inline-edit-knob {
	font-size: 0.85rem;
	position: absolute;
	right: 0.25rem;
}
.simple-edit-wrap {
	position: relative;
}
.simple-edit-wrap .simple-edit-knob {
	font-size: 0.85rem;
	position: absolute;
	right: 0.25rem;
}
</style>

<h1><a href="/inventory">Inventory</a> :: {{ Lot.guid }}</h1>

<div class="row" style="margin-bottom:2em;">
<div class="col-md-6">

<table class="table table-sm table-bordered">
	<tr>
	<td>Product:</td>
	<td>
		<a href="/inventory?product={{ Product.id }}">{{ Product.name }}</a>
		<small>[<a href="/settings/product/edit?id={{ Product.id }}">{{ Product.guid }}</a>]</small>
	</td>
	</tr>
	<tr>
		<td>Product Type:</td>
		<td>{{ Product_Type.name }}</td>
	</tr>
	<tr>
		<td>Strain:</td>
		<td>{{ Variety.name }}
		<?php
		if (!empty($this->Variety['id'])) {
			echo sprintf('<a href="/inventory?strain=%d">%s</a>', $this->Variety['id'], h($this->Strain['name']));
			if (!empty($this->Strain['guid'])) {
				echo ' <small>[<a href="/settings/strains/edit?id=' . $this->Variety['id'] . '">' . UI_GUID::format($this->Strain['guid'], true) . '</a>]</small>';
			}
		}
		?>
		</td>
	</tr>

	<tr>
	<td>Zone:</td>
	<td>{{ Zone.name }}</td>
</tr>
	<tr>
		<td>Quantity:</td>
		<td class="r"><strong>{{ Lot.qty }}</strong></td>
	</tr>

	<tr>
		<td>Sell Price:</td>
		<td>
			<div class="input-group input-group-sm">
				<input
					class="form-control form-control-sm math-input r"
					data-value-current=""
					data-value-initial=""
					id="price" min="0.00" type="number" step="0.01"
					value="{{ Lot.unit_price }}">
				<div class="input-group-append">
					<div class="input-group-text">/each<span id="price-proc-icon"></span></div>
				</div>
			</div>
		</td>
	</tr>
	<!-- <tr>
		<td>Load More...</td>
		<td></td>
	</tr> -->
</table>

<form autocomplete="off" enctype="multipart/form-data" id="form-inventory-edit" method="post">
<div class="form-actions" id="inventory-control-form">
<div id="exec-one-aaa">

	<button accesskey="p" class="btn btn-outline-primary" id="print-exec" name="a" type="button" value="print"><i class="fas fa-print"></i> <u>P</u>rint</button>
	<button accesskey="a" class="btn btn-outline-warning" name="a" title="Adjust the quantity, with documented reason (a)" type="submit" value="adjust"><i class="fas fa-edit"></i> <u>A</u>djust</button>
	<button accesskey="t" class="btn btn-outline-success" name="a" type="submit" value="transfer"><i class="fas fa-truck"></i> Transfer</button>
	<button accesskey="v" class="btn btn-outline-warning" name="a" title="Verify all the data for this Item" type="submit" value="verify"><i class="far fa-check-circle"></i> <u>V</u>erify</button>
	<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modal-lot-finish" name="a" type="button"><i class="far fa-box-check"></i> Finish</button>

	<!--
	<?php
	// if ($show_move) {
	// 	echo ' <button class="btn btn-outline-secondary" name="a" title="Use the Edit link next to Zone (z)" type="submit" value="move"><i class="fas fa-arrow-circle-right"></i> Move</button>';
	// }

	// if ($show_stage) {
	// 	echo ' <button accesskey="m" class="btn btn-outline-primary" name="a" type="submit" value="move"><u>M</u>ove</button>';
	// }

	// echo ' <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-object-note" name="a" type="button"><i class="far fa-comments"></i> Note</button>';

	// echo '<span style="display:none;">';
	// echo '<input id="file-upload"  name="file" type="file">';
	// echo '</span>';
	// echo ' <button class="btn btn-outline-primary" id="exec-file-photo" name="a" type="button"><i class="fas fa-camera"></i> Add Photo</button>';
	// echo ' <button class="btn btn-outline-success" id="exec-file-upload" name="a" style="display:none;" type="submit" value="upload">Upload</button>';

	// if (is_file($img_file)) {
	// 	echo ' <button class="btn btn-outline-primary" id="exec-view-photo" name="a" type="button"><i class="fas fa-image"></i> View Photo</button>';
	// }
	?>
	-->
</div>
<div id="exec-one-ccc">
	<!-- if ($show_destroy) {
		echo ' <button accesskey="x" class="btn btn-outline-danger" name="a" type="submit" value="kill"><i class="fas fa-trash"></i> Destroy</button>';
	}
	if ($show_destroy_undo) {
		echo ' <button class="btn btn-outline-warning" name="a" title="Cancel Schedule for Destruction" type="submit" value="kill-undo">Undo - Destroy</button>';
	} -->
</div>

</div>
</form>

</div> <!-- /.col-md-6 -->

<div class="col-md-6">
	<div class="card">
	<h2 class="card-header">Laboratory Results</h2>
	<div class="card-body">
		<div id="inventory-qa-results"></div>
		<div id="inventory-qa-results-load"></div>
	</div>
	</div>
</div>

</div> <!-- /.row -->

<section>
	<h2>History</h2>
	<table class="table table-sm">
		<thead class="thead-dark">
			<tr>
				<th>Date</th>
				<th>Action</th>
				<th>Change</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{{ Lot.created_at }}</td>
				<th>Inventory Created</th>
				<th>{{ Lot.qty }}</th>
			</tr>
		</tbody>
	</table>
</section>


<script>
function _init_qa_data()
{
	var _load_qa = function(a) {

		var arg = {
			a: a,
			i: '{{ Lot.id }}',
		};

		$('#inventory-qa-results').empty();
		$('#inventory-qa-results').html('<h2 id="inventory-qa-results-load"><i class="fas fa-sync fa-spin"></i> Checking QA Results...</h2>');
		$('#inventory-qa-results').load('/inventory/qa/ajax', arg);

	}

	if ($('#inventory-qa-results-load').length) {
		_load_qa('ping-qa');
	}

	$(document.body).on('click', '#inventory-qa-download', function() {
		window.open($(this).data('link'));
	});

	$(document.body).on('click', '.inventory-qa-refresh-exec', function() {
		_load_qa('ping-qa-refresh');
		return false;
	});

}

function _draw_date_inline($node, action)
{
	var $host = $node.parent();
	$host.css('position', 'relative');

	var $wrap = Weed.Input.hoverMagic();
	$wrap.find('input').attr('type', 'date');
	$wrap.find('input').val( $node.data('date') );

	$host.append($wrap);

	$wrap.show();

	$wrap.one('click', 'button.save', function() {

		var val = $wrap.find('input[type=date]').val();

		if (val) {

			var arg = {
				'a': action,
				'id': null,
				'date': val,
			};

			$.post('/inventory/ajax', arg, function(res) {
				switch (res.status) {
				case 'success':
					$host.html(res.detail);
					break;
				}
				$wrap.remove();
			}).fail(function() {
				alert('date error?');
			});
		}

	});

	$wrap.one('click', 'button.bail', function() {
		$wrap.remove();
	});

	// Save on Enter
	$wrap.on('keydown', 'input', function(e) {
		switch (e.keyCode) {
		case 13:
		case 169:
			$wrap.find('button.save').click();
			return false;
		case 27:
			$wrap.find('button.bail').click();
			return false;
		}
	});

	$wrap.find('input').focus().select();

}

$(function() {

	$('.inline-edit-knob').on('click', function() {

		var $b = $(this);
		var $wrap = $b.closest('.inline-edit-wrap');
		var mode = $wrap.data('mode');

		switch (mode) {
		case 'edit':
			$wrap.find('.inline-edit-edit').hide();
			$wrap.find('.inline-edit-view').show();
			$wrap.data('mode', 'view');
			break;
		case 'view':
		default:
			$wrap.find('.inline-edit-view').hide();
			$wrap.find('.inline-edit-edit').show();
			$wrap.find('.inline-edit-edit select').focus();
			$wrap.data('mode', 'edit');
			break;
		}

	});

	$('#print-exec').on('click', function(e) {
		Weed.Printer.openModal('Inventory', { list: [ '{{ Lot.id }}' ]});
	});

	// $('#price').on('blur change keyup', _.debounce(function() {

	// 	var cur = parseFloat($(this).data('value-current'), 10) || 0;
	// 	var now = parseFloat(this.value, 10) || 0;

	// 	if (now != cur) {

	// 		$('#price-proc-icon').html('<i class="fas fa-sync fa-spin"></i>');

	// 		var arg = {
	// 			a: 'price',
	// 			i: '{{ Lot.id }}',
	// 			p: now,
	// 		};

	// 		$.post('/inventory/ajax', arg, function() {
	// 			$('#price').data('value-current', arg.p);
	// 			$('#price-proc-icon').empty();
	// 		});

	// 		$('#price-proc-icon').empty();

	// 	}

	// }, 500));

	$('#inventory-wet-date-edit').on('click', function() {
		_draw_date_inline($(this), 'update-wet-collect-date');
	});

	/**
		Update Dry Date
	*/
	$('#inventory-dry-date-edit').on('click', function() {
		_draw_date_inline($(this), 'update-dry-collect-date');
	});

	/**
		File Upload Handler
	*/
	$('#file-upload').on('change', function(e) {
		$('#exec-file-photo').hide();
		$('#exec-file-upload').show();
		//addClass('good').removeClass('exec');
		//$('#exec-file-upload').attr('type', 'submit');
		//$('#exec-file-upload').html('Upload');
	});

	$('#exec-file-photo').on('click', function(e) {
		$('#file-upload').click();
	});

	$('#exec-view-photo').on('click', function(e) {
		// Modal
		$('#view-image-modal').dialog({
			width: (window.screen.width * 0.75),
		});
	});

	// Load QA Results
	_init_qa_data();

	// Load Parents
	var arg = {
		a: 'parents',
		id: '{{ Lot.id }}',
	};
	$('#inventory-parent-wrap').load('/inventory/ajax', arg);

});
</script>
