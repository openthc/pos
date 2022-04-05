<?php
/**
 *
 */

namespace App\Controller\Shop;

class Example extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = $this->data;

		$dbc_auth = _dbc('auth');
		$Company = $dbc_auth->fetchRow('SELECT id, name, dsn FROM auth_company WHERE id = :c0', [ ':c0' => $_GET['c'] ]);
		if (empty($Company['id'])) {
			_exit_html_fail('Invalid Request [CSE-020]', 400);
		}

		$dbc_user = _dbc($Company['dsn']);
		unset($Company['dsn']);

		$data['Page']['title'] = sprintf('Example Shop :: %s', $Company['name']);
		$data['Company'] = $Company;

		$sql = <<<SQL
 SELECT inventory.id,
    inventory.id AS lot_id,
    inventory.license_id,
    inventory.created_at,
    inventory.guid,
    inventory.stat,
    inventory.flag,
    inventory.qty,
    inventory.qa_cbd AS cbd,
    inventory.qa_thc AS thc,
    COALESCE(inventory.sell, product.sell) AS sell,
    inventory.tags,
    inventory.variety_id AS variety_id,
    variety.name AS variety_name,
    inventory.product_id,
    product.name AS product_name,
    product.package_type,
    product.package_pack_qom,
    product.package_pack_uom,
    product.package_unit_qom,
    product.package_unit_uom,
    product.package_dose_qty,
    product.package_dose_qom,
    product.package_dose_uom,
    product_type.id AS product_type_id,
    product_type.name AS product_type_name,
    product_type.mode AS product_type_mode,
    product_type.unit AS product_type_unit
   FROM inventory
     JOIN variety ON inventory.variety_id::text = variety.id::text
     JOIN product ON inventory.product_id::text = product.id::text
     JOIN product_type ON product.product_type_id::text = product_type.id::text
    WHERE product_type.id NOT IN ('018NY6XC00PR0DUCTTYPE00000', '018NY6XC00PR0DUCTTYPE00001', '018NY6XC00PR0DUCTTYPETY5AT', '018NY6XC00PT8AXVZGNZN3A0QT')
		AND inventory.stat = 200 AND inventory.qty > 0
	ORDER BY product_type_name, product_name, package_unit_uom
SQL;
		$data['product_list'] = $dbc_user->fetchAll($sql);

		$html = $this->render('shop/example.php', $data);

		return $RES->write($html);

	}
}
