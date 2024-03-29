<?php
/**
 * A Sale Item
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\B2C\Sale;

class Item extends \OpenTHC\SQL\Record
{
	protected $_table = 'b2c_sale_item';

	const FLAG_TAX_EXCISE = 0x00010000;
	const FLAG_TAX_RETAIL = 0x00020000;

}
