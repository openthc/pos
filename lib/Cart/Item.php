<?php
/**
 *
 */

namespace OpenTHC\POS\Cart;

class Item
{
	// public $item_count       = 0;
	public $unit_count       = 0;
	public $unit_price_total = 0;
	public $tax_rate_data    = [];
	public $tax_total        = 0;
	public $full_price       = 0;

	function __construct($item)
	{
		// $chk = json_decode($chk);
		foreach ($item as $k => $v) {
			$this->{$k} = $v;
		}

		$this->unit_price_total = $this->unit_price * $this->unit_count;

		// Format
		$this->unit_price = sprintf('%0.2f', $this->unit_price);
		$this->unit_price_total = sprintf('%0.2f', $this->unit_price_total);
		// $b2c_item->full_price = sprintf('%0.2f', $b2c_item->full_price);

	}

}
