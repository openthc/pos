<?php
/**
 * Report AJAX Helper
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\Controller\Report;

use Edoceo\Radix\Session;

class Ajax extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = $this->_container->DB;

		switch ($_POST['a']) {
			case 'push-transaction':
				$B2C = $_POST['id'];
				$sql = 'SELECT * FROM b2c_sale WHERE id = :pk AND license_id = :l0';
				$B2C = $dbc->fetchRow($sql, [
					':pk' => $B2C,
					':l0' => $_SESSION['License']['id'],
				]);
				$B2C = new \App\B2C\Sale($dbc, $B2C);
				if ($B2C['id']) {
					return $this->_push_transaction($RES, $B2C);
				}

				return $RES->withStatus(404);
				break;
		}
	}

	function _push_transaction($RES, $B2C)
	{
		$dbc = $this->_container->DB;

		$cfg = \OpenTHC\CRE::getEngine($_SESSION['cre']['id']);
		$cfg = array_merge($_SESSION['cre'], $cfg);
		$cre = \OpenTHC\CRE::factory($cfg);
		$cre->setLicense($_SESSION['License']);

		$meta = $B2C->getMeta();
		$b2c_sale_item_list = [];
		foreach ($meta as $key => $value) {
			if ( ! preg_match('/^qty-(\w{26})$/', $key, $match)) {
				continue;
			}
			$I = $dbc->fetchRow('SELECT * FROM inventory WHERE id = :pk AND license_id = :l0', [
				':pk' => $match[1],
				':l0' => $_SESSION['License']['id'],
			]);

			$sql = 'SELECT * FROM b2c_sale_item';
			$sql.= ' WHERE b2c_sale_id = :pk';
			$sql.= ' AND inventory_id = :i0';
			$b2c_sale_item = $dbc->fetchRow($sql, [
				':pk' => $B2C['id'],
				':i0' => $I['id'],
			]);
			$b2c_sale_item = new \App\B2C\Sale\Item($dbc, $b2c_sale_item);
			$b2c_sale_item_list[] = [
				'inventory' => $I,
				'sale_item' => $b2c_sale_item,
			];
		}

		switch ($cre::ENGINE) {
			case 'metrc':

				$d = new \DateTime($B2C['created_at']);
				$obj = array(
					'SalesDateTime' => $d->format(\DateTime::ISO8601),

					// @todo We need to have a UX for Patients and Caregivers with Identifiers
					// Requires: Correct Facility Type
					// 'SalesCustomerType' => 'Consumer', // @see GET /sales/v1/customertypes?
					// 'PatientLicenseNumber' => null,
					// 'CaregiverLicenseNumber' => null,

					// 'SalesCustomerType' => 'ExternalPatient', // @see GET /sales/v1/customertypes?
					// 'PatientLicenseNumber' => null,
					// 'CaregiverLicenseNumber' => null,

					// Requires: Patient Number
					// Requires: Caregiver Number
					// 'SalesCustomerType' => 'Caregiver', // @see GET /sales/v1/customertypes?
					// 'PatientLicenseNumber' => '000001',
					// 'CaregiverLicenseNumber' => '000002',

					// Requires: Patient Number
					'SalesCustomerType' => 'Patient', // @see GET /sales/v1/customertypes?
					'PatientLicenseNumber' => '000001',
					'CaregiverLicenseNumber' => null,

					'IdentificationMethod' => null,
					'PatientRegistrationLocationId' => null,
					'Transactions' => array(),
				);
				foreach ($b2c_sale_item_list as $b2c_sale_item) {
					$sale_item = $b2c_sale_item['sale_item'];

					// $uom = new \OpenTHC\UOM($sale_item['uom']);
					switch ($sale_item['uom']) {
						case 'ea':
							$uom = 'Each';
							break;
						case 'g':
							$uom = 'Grams';
							break;
					}

					$transaction = array(
						'PackageLabel' => $I['guid'],
						'Quantity' => $sale_item['unit_count'],
						// 'UnitOfMeasure' => $uom->getName(),
						'UnitOfMeasure' => $uom,
						'TotalAmount' => $sale_item['unit_price'],
						'UnitThcPercent' => null,
						'UnitThcContent' => null,
						'UnitThcContentUnitOfMeasure' => null,
						'UnitWeight' => null,
						'UnitWeightUnitOfMeasure' => null,
					);
					$obj['Transactions'][] = $transaction;
				}

				$res = $cre->b2c()->create($obj);
				switch ($res['code']) {
					case 200:
						// $chk = $cre->b2c()->sync(); // @todo
						// $chk = $cre->b2c()->transactions()->sync(); // @todo
						// $chk = $cre->lot()->sync(); // @todo
						$RES = $RES->withStatus(201)
							->withHeader('Content-type', 'application/json')
							->withJson([
								'data' => [
									'b2c_list' => [$B2C->toArray()]
								],
								'meta' => [],
							]);
						;
						break;

					default:
						\Edoceo\Radix\Session::flash('fail', $cre->formatError());
						$RES = $RES->withStatus(502);
				}

				break;

			case 'openthc':
			default:
		}

		return $RES;
	}
}
