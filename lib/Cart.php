<?php
/**
 * A Cart Cache Wrapper
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS;

use \OpenTHC\POS\Cart\Item;

class Cart
{
	public $id;

	public $key;

	public $item_count       = 0;
	public $unit_count       = 0;
	public $unit_price_total = 0;
	public $tax_total        = 0;
	public $tax_rate_data    = null;
	public $full_price       = 0;

	public $item_list = null;

	public $Contact;

	private $rdb;

	/**
	 *
	 */
	function __construct($rdb, $key=null)
	{
		$this->rdb = $rdb;

		if (empty($key)) {
			$key = _ulid();
		}

		$this->id = $key;
		$this->key = sprintf('/%s/cart/%s', $_SESSION['License']['id'], $this->id);
		$this->type = 'REC';
		$this->item_list = new \stdClass();
		$this->tax_rate_data = new \stdClass();

		$chk = $this->rdb->get($this->key);
		if ( ! empty($chk)) {
			$chk = json_decode($chk);
			foreach ($chk as $k => $v) {
				$this->{$k} = $v;
			}
		}

		if ( empty($this->item_list)) {
			$this->item_list = new \stdClass();
		}

	}

	function setTaxRateData($tax_rate_data)
	{
		$this->tax_rate_data = $tax_rate_data;
	}

	function save()
	{
		$val = json_encode($this);

		// $val = [];
		// $val['id'] = $this->id;
		// $val['key'] = $this->key;
		// $val['name'] = $this->name;
		// $val['Contact'] = $this->contact;
		// $val['item_list'] = $this->item_list;

		$this->rdb->set($this->key, $val, [ 'ex' => 86400 ]);

	}

	function addItem(Item $b2c_item)
	{
		if ( ! empty($this->item_list->{ $b2c_item->id })) {
			// Adding to Existing
			$this->item_list->{ $b2c_item->id }->unit_count = $b2c_item->unit_count;
			$this->updateTotals();
			return;
		}

		// Taxes included in Price already
		if ($this->tax_rate_data->tax_included) {

			$total_price_want = $b2c_item->unit_price;

			// Do Back-Calc
			$tax_rate_list = [];
			foreach ($this->tax_rate_data->tax_rate_list as $tax_ulid => $tax_rate) {
				$tax_rate_list[] = $tax_rate->rate;
			}

			$tax_vector = 1 + array_sum($tax_rate_list);
			$tax_vector_inverse = 1 / $tax_vector;

			$b2c_item->base_price = $b2c_item->unit_price * $tax_vector_inverse;
			$b2c_item->base_price = round($b2c_item->base_price, 2);

			// Add Taxes
			$b2c_item->tax_rate_data = new \stdClass();
			foreach ($this->tax_rate_data->tax_rate_list as $tax_ulid => $tax_rate) {

				$trd_item = new \stdClass();
				$trd_item->id = $tax_ulid;
				$trd_item->name = $tax_rate->name;
				$trd_item->rate = $tax_rate->rate;
				$trd_item->amount = round($b2c_item->base_price * $tax_rate->rate, 2);

				// Legacy Names
				$trd_item->tax_rate = $trd_item->rate; // v0
				$trd_item->tax_amount = $trd_item->amount; // v0

				$b2c_item->tax_rate_data->{ $tax_ulid } = $trd_item;

				$b2c_item->tax_total += $trd_item->amount;
			}

			$b2c_item->base_price = $total_price_want - $b2c_item->tax_total;

			$b2c_item->full_price = ($b2c_item->base_price + $b2c_item->tax_total) * $b2c_item->unit_count;
			$b2c_item->full_price = round($b2c_item->full_price, 2);

			if ($b2c_item->full_price != $total_price_want) {
				// Need to Apply Some Rounding Here!
				throw new \Exception('Price Tax Calculation Failure');
			}

		} else {
			foreach ($this->tax_rate_data->tax_rate_list as $tax_ulid => $tax_rate) {
				$tax_amount = $b2c_item->unit_price_total * $tax_rate;
				$b2c_item->tax_rate_data->{$tax_ulid} = $tax_amount;
				$b2c_item->tax_total += $tax_amount;
			}
		}

		$this->item_list->{ $b2c_item->id } = $b2c_item;

		$this->updateTotals();
	}

	function delItem($b2c_item_id)
	{
		unset($this->item_list->{$b2c_item_id});
		$this->updateTotals();
	}

	/**
	 * Update Cart Totals
	 */
	protected function updateTotals()
	{
		$this->item_count       = 0;
		$this->base_price_total = 0;
		$this->unit_count       = 0;
		$this->unit_price_total = 0;
		$this->tax_total        = 0;
		$this->full_price       = 0;

		foreach ($this->item_list as $oid => $obj) {

			$this->item_count++;
			$this->unit_count       += $obj->unit_count;

			$this->base_price_total += ($obj->base_price * $obj->unit_count);
			$this->unit_price_total += ($obj->unit_price * $obj->unit_count);

			$this->tax_total        += ($obj->tax_total * $obj->unit_count);
			$this->full_price       += ($obj->full_price * $obj->unit_count);
		}

		$this->unit_price_total = round($this->unit_price_total, 2);
		$this->tax_total  = round($this->tax_total, 2);
		$this->full_price = round($this->full_price, 2);

	}
}
