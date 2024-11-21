<?php
/**
 * A Cart Cache Wrapper
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS;

class Cart
{
	public $id;

	public $key;

	public $item_count       = 0;
	public $unit_count       = 0;
	public $unit_price_total = 0;
	public $tax_total        = 0;
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
		$this->item_list = new \stdClass();

		$chk = $this->rdb->get($this->key);
		if ( ! empty($chk)) {
			$chk = json_decode($chk);
			foreach ($chk as $k => $v) {
				$this->{$k} = $v;
			}
		}

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

}
