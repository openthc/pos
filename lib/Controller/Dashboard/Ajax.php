<?php
/**
 * Dashboard AJAX Handler
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Dashboard;

class Ajax extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		switch ($_GET['a']) {
			case 'b2c-revenue-daily':
				$this->_b2c_revenue_daily();
				break;
			case 'b2c-revenue-daily-product-type':
				$this->_b2c_revenue_daily_product_type();
				break;
			case 'b2c-recent':
				$this->_b2c_recent();
				break;
		}

		__exit_json([
			'data' => null,
			'meta' => [ 'detail' => 'Invalid Request [CDA-029]' ],
		], 404);

	}

	/**
	 * Return JSON of Daily Revenue
	 */
	function _b2c_revenue_daily()
	{
		$cht_data = [];
		$cht_data['type'] = 'bar';
		$cht_data['data'] = [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Revenue',
					'backgroundColor' => 'green',
					'borderColor' => 'green',
					'data' => []
				]
			]
		];
		$cht_data['options'] = [
			'animations' => false,
			'maintainAspectRatio' => false,
		];

		$sql = <<<SQL
SELECT sum(b2c_sale.full_price) AS full_price_sum
 , date_trunc('day', created_at) AS created_at_day
FROM b2c_sale
WHERE created_at >= now() - '15 days'::interval
GROUP BY created_at_day
ORDER BY created_at_day
SQL;

		$dbc = $this->_container->DB;
		$res = $dbc->fetchAll($sql);

		foreach ($res as $rec) {
			$cht_data['data']['labels'][] = $rec['created_at_day'];
			$cht_data['data']['datasets'][0]['data'][] = $rec['full_price_sum'];
		}

		__exit_json([
			'data' => $cht_data,
			'meta' => [ 'detail' => 'Query Not Executed [CDA-076]' ]
		]);

	}

	/**
	 * Return JSON of Daily Revenue by Product Type
	 */
	function _b2c_revenue_daily_product_type()
	{
		$sql = <<<SQL
SELECT sum(b2c_sale_item.unit_price * b2c_sale_item.unit_count) AS full_price_sum
, product_type.name AS product_type_name
FROM b2c_sale_item
JOIN b2c_sale ON b2c_sale_item.b2c_sale_id = b2c_sale.id
JOIN inventory ON b2c_sale_item.inventory_id = inventory.id
JOIN product ON inventory.product_id = product.id
JOIN product_type ON product.product_type_id = product_type.id
WHERE b2c_sale.created_at >= now() - '15 days'::interval
GROUP BY product_type.name
ORDER BY full_price_sum DESC
SQL;

		$dbc = $this->_container->DB;
		$res = $dbc->fetchAll($sql);

		$cht_data = [];
		$cht_data['type'] = 'doughnut'; // 'pie';
		$cht_data['data'] = [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Revenue by Product Type',
					'backgroundColor' => [ 'red', 'orange', 'yellow', 'green', 'blue', 'indigo', 'violet' ],
					// 'borderColor' => 'green',
					'data' => []
				]
			]
		];
		$cht_data['options'] = [
			'animations' => false,
			'maintainAspectRatio' => false,
			'plugins' => [
				'legend' => false,
			]
		];

		foreach ($res as $rec) {
			$cht_data['data']['labels'][] = $rec['product_type_name'];
			$cht_data['data']['datasets'][0]['backgroundColor'][] =
			$cht_data['data']['datasets'][0]['data'][] = $rec['full_price_sum'];
		}

		__exit_json([
			'data' => $cht_data,
			'meta' => [], // 'detail' => 'Results Not Reay [CDA-097]' ]
		]);

	}

	/**
	 *
	 */
	function _b2c_recent()
	{
		$dbc = $this->_container->DB;

		// $sql = 'SELECT b2c_sale.*, count(b2c_sale_item.id) AS b2c_sale_item_count FROM b2c_sale JOIN b2c_sale_item ON b2c_sale.id = b2c_sale_item.b2c_sale_id  ORDER BY b2c_sale.created_at DESC LIMIT 10';
		// $sql = 'SELECT b2c_sale.*, (SELECT count(b2c_sale_item.id) FROM b2c_sale_item WHERE b2c_sale_item.b2c_sale_id = b2c_sale.id) AS b2c_sale_item_count FROM b2c_sale ORDER BY b2c_sale.created_at DESC LIMIT 10';
		$sql = <<<SQL
		SELECT b2c_sale.id
			, b2c_sale.created_at
			, b2c_sale.item_count
			, b2c_sale.full_price
			, b2c_sale.contact_id
			, b2c_sale.contact_id_client
		FROM b2c_sale
		WHERE license_id = :l0
		ORDER BY b2c_sale.created_at DESC
		LIMIT 10
		SQL;
		$arg = [
			':l0' => $_SESSION['License']['id'],
			// ':s0' => 200,
		];
		$res = $dbc->fetchAll($sql, $arg);
		?>
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
					<th>Time</th>
					<th>Register</th>
					<th>Status</th>
					<th class="r">Items</th>
					<th class="r">Amount</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($res as $rec) {
				echo '<tr>';
				echo '<td>' . _date('m/d h:i', $rec['created_at']) . '</td>';
				echo '<td>' . $rec['contact_id'] . '</td>';
				echo '<td>' . $rec['stat'] . '</td>';
				echo '<td class="r">' . $rec['item_count'] . '</td>';
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
