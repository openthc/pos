<?php
/**
 * Tax Rate Loader Helper
 */

namespace OpenTHC\POS\Feature;

trait LoadTaxRateInfo
{
	protected $tax_info;

	function loadTaxRateInfo()
	{
		$cache_key = sprintf('/%s/pos/b2c/item/adjust-list', $_SESSION['License']['id']);

		// Load Tax Data
		$rdb = $this->_container->Redis;

		// Check Cache
		// $tax_info = $rdb->get($cache_key);
		// if ( ! empty($tax_info)) {
		// 	$this->tax_info = json_decode($tax_info);
		// 	return $this->tax_info;
		// }

		$dbc = $this->_container->DB;

		$Company = new \OpenTHC\Company($dbc, $_SESSION['Company']);
		$License = new \OpenTHC\License($dbc, $_SESSION['License']['id']);

		$this->tax_info = new \stdClass();
		$x = $Company->getOption(sprintf('/%s/b2c-item-price-adjust/tax-included', $License['id']));
		$this->tax_info->tax_included = intval($x);

		$this->tax_info->tax_rate_list = [];

		$r = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0SST03Q484J', $License['id'])));
		if ($r) {
			$this->tax_info->tax_rate_list['010PENTHC00BIPA0SST03Q484J'] = [
				'name' => 'State Sales Tax',
				'rate' => $r / 100
			];
		}
		$r = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0C0T620S2M2', $License['id'])));
		if ($r) {
			$this->tax_info->tax_rate_list['010PENTHC00BIPA0C0T620S2M2'] = [
				'name' => 'County Sales Tax',
				'rate' => $r / 100
			];
		}
		$r = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0CIT5H9S6T3', $License['id'])));
		if ($r) {
			$this->tax_info->tax_rate_list['010PENTHC00BIPA0CIT5H9S6T3'] = [
				'name' => 'City Sales Tax',
				'rate' => $r / 100
			];
		}
		$r = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0MUT0FEEGCF', $License['id'])));
		if ($r) {
			$this->tax_info->tax_rate_list['010PENTHC00BIPA0MUT0FEEGCF'] = [
				'name' => 'Regional Sales Tax',
				'rate' => $r / 100
			];
		}
		$r = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0ET0FNBCKMH', $License['id'])));
		if ($r) {
			$this->tax_info->tax_rate_list['010PENTHC00BIPA0ET0FNBCKMH'] = [
				'name' => 'Cannabis Excise Tax',
				'rate' => $r / 100
			];
		}

		$val = json_encode($this->tax_info);
		$rdb->set($cache_key, $val, [ 'ex' => '1800' ]);

		return $this->tax_info;

	}
}
