<?php
/**
 * POS AJAX Handler
*/

namespace App\Controller\POS;

class Ajax extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		session_write_close();

		switch ($_GET['a']) {
		case 'hold-list':
			$data = array('hold_list' => array());
			$res = $this->_container->DB->fetchAll('SELECT * FROM b2c_sale_hold ORDER BY created_at');
			//var_dump($res);
			foreach ($res as $rec) {

				$rec['meta'] = json_decode($rec['meta'], true);
				$info = array();
				foreach ($rec['meta'] as $k => $v) {
					if (preg_match('/^qty\-(\d+)$/', $k, $m)) {
						$info[] = sprintf('%s=%d', $k, $v);
					}
				}

				$data['hold_list'][] = array(
					'id' => $rec['id'],
					'time' => _date('h:i', $rec['cts']),
					'name' => $rec['meta']['name'],
					'item_info' => implode(', ', $info),
				);
			}
			return $this->_container->view->render($RES, 'block/hold-list.html', $data);

			case 'push':

				$k = sprintf('pos-terminal-card', $_SESSION['pos-terminal-id']);
				$this->_container->Redis->del($k);
				$x = $this->_container->Redis->set($k, json_encode($_POST));

				return $RES->withJSON(array(
					'data' => null,
					'meta' => [ 'detail' => 'success' ],
				));

			break;
			case 'search':
				return $this->_search($RES);
			break;
		}

		__old_ajax_shit($RES);

	}

	function _search($RES)
	{
		$q = trim($_GET['q']);
		switch (substr($q, 0, 1)) {
		case '~':
			// Exact Match?
			$q = substr($q, 1);
		}

		// Starts or Ends with the Code
		// $res = \App\POS::listInventory("%{$q}%");
		$sql = <<<SQL
SELECT lot_full.*
FROM lot_full
WHERE license_id = :l0
  AND stat = 200
  AND qty > 0
  AND (sell IS NOT NULL AND sell > 0)
SQL;
		$arg = array(
			':l0' => $_SESSION['License']['id'],
		);

		if (!empty($q)) {
			$sql.= ' AND guid LIKE :q1';
			// $arg[':q0'] = $q;
			$arg[':q1'] = sprintf('%%%s%%', $q);
		}
		$sql.= ' ORDER BY product_type_name, product_name';
		$res = $this->_container->DB->fetchAll($sql, $arg);

		switch (count($res)) {
		case 0:
			_draw_ajax_search_error();
			return $RES->withStatus(404);
			exit(0);
		default:

			// _draw_inventory_grid($res);
			_draw_inventory_list($res);

			if (1 == count($res)) {

				$rec = $res[0];

				//echo '<script>';
				////echo 'addSaleItem(document.getElementById("inv-item-' . $rec['id'] . '"));';
				////echo '$("#barcode-auto-complete").empty();';
				////echo '$("#barcode-auto-complete").hide();';
				////echo '$("#barcode-input").val("");';
				////echo 'searchInventory("");';
				//echo '</script>';

			}
		}

	}
}


function __old_ajax_shit($RES)
{
	switch ($_GET['a']) {
	case 'discount-list':

		echo '<div style="padding: 8px;">';
		echo '<div style="border: 2px inset #444; padding: 4px;">';
		echo '<h3 style="border-bottom: 1px solid #999;">Discount A</h3>';
		echo '<h3 style="border-bottom: 1px solid #999;">Discount B</h3>';
		echo '<h3 style="border-bottom: 1px solid #999;">Discount C</h3>';

		echo '</div>';
		echo '</div>';
		break;

	case 'ping':

		if (!empty($_SESSION['pos-terminal-id'])) {
			$k = "pos-terminal-{$_SESSION['pos-terminal-id']}";
			$this->_container->Redis->hset($k, 'ping', $_SERVER['REQUEST_TIME']);
		}

		header('content-type: application/javascript');
		die('// pong');

		break;

	case 'pull':

		$chk = $this->_container->Redis->hget('pos-terminal-' . $_SESSION['pos-terminal-id'] . '-cart');
		if (empty($chk)) {
			echo '<div class="col-md-12">';
			echo '<h1>Cart is Empty</h1>';
			echo '</div>';
			exit(0);
		}
		ksort($chk);

		// $last = $_SESSION['pos-last-cart-draw-hash'];
		// $hash = md5(serialize($chk));
		// if ($last == $hash) {
		// 	header('HTTP/1.1 304 Not Modified', true, 304);
		// 	exit(0);
		// }

		foreach ($chk as $k => $v) {
			if (preg_match('/^item\-(\d+)$/', $k , $m)) {

				$I = new Inventory($m[1]);
				$si = $this->_container->Redis->hget('strain/' . strtolower($I['name']));

				echo '<div class="col-md-9">';
				echo '<h2>';
				if ($chk["size-{$I['id']}"] > 1) {
					echo ($chk["size-{$I['id']}"] . 'x ');
				}
				echo h($I['name']);
				if (!empty($si['kind'])) echo ' (' . $si['kind'] . ')';
				echo '</h2>';
				echo '</div>';
				echo '<div class="col-md-3" style="text-align:right;">';
				echo number_format($I['sell'] * $chk["size-{$I['id']}"], 2);
				echo '</div>';

				// Description
				if (!empty($si['text'])) {
					echo '<div class="col-md-12">';
					echo '<p>' . h($si['text']) . '</p>';
					echo '</div>';
				}

				$img_list = array();
				foreach ($si as $k => $v) {
					if (preg_match('/^photo-(\d+)-mini$/', $k, $m)) {
						$img_list[] = '<img src="' . preg_replace('/^http:/', 'https:', $v) . '" style="margin:0px 0px 4px 4px;">';
					}
				}
				echo '<div class="col-md-12">' . implode('', $img_list) . '</div>';

				$flv_list = array();
				foreach ($si as $k => $v) {
					if (preg_match('/^flavor-(.+)$/', $k, $m)) {
						$buf = '<div class="col-md-8"><h3>' . h($m[1]) . '</h3></div>';
						$buf.= '<div class="col-md-4">';
						$buf.= '<div class="meter green nostripes"><span style="text-align:center; width: ' . floatval($v) . '%">' . sprintf('%0.1f', floatval($v)) . '%</span></div>';
						$buf.= '</div>';
						$flv_list[] = $buf;
					}
				}
				if (count($flv_list)) {
					echo '<div class="col-md-12">';
					echo implode('', $flv_list);
					echo '</div>';
				}

				$neg_list = array();
				foreach ($si as $k => $v) {
					if (preg_match('/^negative-(.+)$/', $k, $m)) {
						$buf = '<div class="col-md-8"><h3>' . h($m[1]) . '</h3></div>';
						$buf.= '<div class="col-md-4">';
						$buf.= '<div class="meter orange nostripes"><span style="text-align:center; width: ' . floatval($v) . '%">' . sprintf('%0.1f', floatval($v)) . '%</span></div>';
						$buf.= '</div>';
						$neg_list[] = $buf;
					}
				}
				if (count($neg_list)) {
					echo '<div class="col-md-12">';
					echo implode('', $neg_list);
					echo '</div>';
				}

			}
		}

		echo '</div>';

		$_SESSION['pos-last-cart-draw-hash'] = $hash;

		break;

	}

	exit(0);

}

function _draw_ajax_search_error()
{
		Session::flash('fail', 'Item not found');
?>
<div id="alert-lookup">
<?= Session::flash() ?>
</div>
<script>
$(function() {
	setTimeout(function() {
		$('#barcode-input').val('');
	}, 750);
	setTimeout(function() {
		$('#barcode-auto-complete').empty();
		$('#barcode-auto-complete').hide();
	}, 3210);
});
</script>
<?php
}


/**
 * Draw in a Grid Layout
 * Four Columns, Picture,
 */
function _draw_inventory_grid($res)
{
	echo '<div class="pos-item-grid">';

	echo '<div class="pos-item-grid-head text-center">';
	echo '<div class="btn-group btn-group-sm">';
	echo '<button class="btn btn-outline-secondary">Flower</button>';
	echo '<button class="btn btn-outline-secondary">Concentrates</button>';
	echo '<button class="btn btn-outline-secondary">Edibles</button>';
	echo '</div>';
	echo '</div>';

	echo '<div class="pos-item-grid-body">';
	foreach ($res as $rec) {

		//$I = new Inventory($rec);
		$rec['name'] = $rec['product_name'] . '/' . $rec['strain_name'];

		// if ($pt_x != $rec['product_type_id']) {
		// 	echo '<h3>' . h($rec['product_type_name']) . '</h3>';
		// }
		// $pt_x = $rec['product_type_id'];

		echo '<div class="pos-item-grid-item"';
		echo ' data-id="' . $rec['id'] . '"';
		echo ' data-name="' . substr($rec['guid'], -4) . ': ' . h($rec['name']) . '"';
		echo ' data-count="' . sprintf('%0.2f', $rec['unit_onhand']) . '"';
		echo ' data-weight="' . sprintf('%0.2f', $rec['unit_weight']) . '"';
		echo ' data-price="' . sprintf('%0.2f', $rec['sell']) . '"';
		echo ' id="inv-item-' . $rec['id'] . '">';

		// Header Bar
		echo '<div>';
		echo '<h4>';
		echo substr($rec['guid'], -4);
		echo ': ';
		echo h($rec['name']);
		echo '</h4>';
		echo '</div>';

		// Image
		echo '<div>';
		if (!empty($rec['product_ulid'])) {
			echo sprintf('<img class="img-fluid" src="/img/product/%s.png">', $rec['product_ulid']);
		} else {
			echo '<div class="text-center" style="font-size:100px;"><i class="fas fa-cannabis"></i></div>';
		}

		echo '</div>';

		// Details
		echo '<div style="display:flex;">';

			echo '<div style="flex: 1 1 60%;">';
			switch (sprintf('%s/%s', $rec['product_type_mode'], $rec['product_type_unit'])) {
			case 'each/ea':
				echo '<h4 style="text-align:right;">' . sprintf('%0.2f', $rec['unit_weight']) . ' ea</h4>';
				break;
			case 'each/g':
				echo '<h4 style="text-align:right;">' . sprintf('%0.2f', $rec['unit_weight']) . ' g</h4>';
				break;
			default:
				echo '<h4 style="text-align:right;">' . sprintf('%d * %0.2f g', $rec['unit_onhand'], $rec['unit_weight']) . '/' . $rec['product_name'] . '</h4>';
				break;
			}
			echo '</div>';

			// Price
			echo '<div style="flex: 1 1 40%;">';
			echo '<h4 style="text-align:right;">$' . number_format($rec['sell'], 2) . '</h4>';
			echo '</div>';

		echo '</div>';

		echo '</div>';

	}

	echo '</div>'; // /.pos-item-grid-grid
	echo '</div>'; // /.pos-item-grid

}

function _draw_inventory_list($res)
{
	// Default Mode -- Draw All Inventory
	foreach ($res as $rec) {

		//$I = new Inventory($rec);
		$rec['name'] = sprintf('%s / %s', $rec['product_name'], $rec['strain_name']);
		$rec['name'] = trim($rec['name'], '/');

		if ($pt_x != $rec['product_type_id']) {
			echo '<h3>' . h($rec['product_type_name']) . '</h3>';
		}
		$pt_x = $rec['product_type_id'];

		echo '<div class="inv-item row"';
		echo ' data-id="' . $rec['id'] . '"';
		echo ' data-name="' . substr($rec['guid'], -4) . ': ' . h($rec['name']) . '"';
		echo ' data-count="' . sprintf('%d', $rec['qty']) . '"';
		echo ' data-weight="' . sprintf('%0.1f', $rec['package_unit_qom']) . '"';
		echo ' data-price="' . sprintf('%0.2f', $rec['sell']) . '"';
		echo ' id="inv-item-' . $rec['id'] . '">';

		echo '<div class="col-md-7">';
		echo '<h4>';
		echo substr($rec['guid'], -4);
		echo ': ';
		echo h($rec['name']);
		echo '</h4>';
		echo '</div>';

		echo '<div class="col-md-2">';
		switch ($rec['product_type_mode']) {
		case 'bulk':
			printf('<h4 style="text-align:right;">%0.2f %s</h4>', $rec['package_unit_qom'], $rec['package_unit_uom']);
			break;
		case 'each':
			printf('<h4 style="text-align:right;">%0.2f %s</h4>', $rec['package_unit_qom'], $rec['package_unit_uom']);
			break;
		}
		echo '</div>';

		echo '<div class="col-md-3">';
		echo '<h4 style="text-align:right;">$' . number_format($rec['sell'], 2) . '</h4>';
		echo '</div>';

		echo '</div>';

	}
}
