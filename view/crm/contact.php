
<div class="row mt-4">
<div class="col">
<div class="container">

<h1>Customer Contact List</h1>

<form autocomplete="off" method="post">
<table class="table table-sm">
<thead class="thead-dark">
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th>Phone</th>
		<th>Tags</th>
		<th></th>
	</tr>
</thead>
<tbody>
<?php
foreach ($data['contact_list'] as $c) {
	echo '<tr>';
	printf('<td>%s</td>', __h($c['fullname']) );
	printf('<td>%s</td>', $c['email'] );
	printf('<td>%s</td>', $c['phone'] );
	echo '</tr>';
}
?>
</tbody>
<tfoot>
	<tr>
		<td><input class="form-control form-control-sm" name="contact-name" type="text"></td>
		<td><input class="form-control form-control-sm" name="contact-email" type="text"></td>
		<td><input class="form-control form-control-sm" name="contact-phone" type="text"></td>
		<td><input class="form-control form-control-sm" name="contact-tags" type="text"></td>
		<td class="r"><button class="btn btn-sm btn-outline-primary" name="a" value="contact-save"><i class="fas fa-save"></i></button></td>
	</tr>
</tfoot>
</table>
</form>

</div>
</div>
</div>
