<?php
/**
 * Home AJAX Handler
 */

namespace App\Controller\Home;

class Ajax extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		// $sql = 'SELECT b2c_sale.*, count(b2c_sale_item.id) AS b2c_sale_item_count FROM b2c_sale JOIN b2c_sale_item ON b2c_sale.id = b2c_sale_item.b2c_sale_id  ORDER BY b2c_sale.created_at DESC LIMIT 10';
		$sql = 'SELECT b2c_sale.*, (SELECT count(b2c_sale_item.id) FROM b2c_sale_item WHERE b2c_sale_item.b2c_sale_id = b2c_sale.id) AS b2c_sale_item_count FROM b2c_sale ORDER BY b2c_sale.created_at DESC LIMIT 10';
		$arg = [
			// ':l0' => $_SESSION['License']['id'],
			// ':s0' => 200,
		];
		$res = $dbc->fetchAll($sql, $arg);
		// var_dump($res);
?>
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
					<th>Time</th>
					<th>Register</th>
					<th class="r">Items</th>
					<th class="r">Amount</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($res as $rec) {
				echo '<tr>';
				echo '<td>' . _date('h:i', $rec['created_at']) . '</td>';
				echo '<td>' . '???' . '</td>';
				echo '<td class="r">' . $rec['b2c_sale_item_count'] . '</td>';
				echo '<td class="r">' . number_format($rec['full_price'], 2) . '</td>';
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
<?php
		exit(0);
	}
}
