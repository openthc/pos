<?php
/**
 * PickTicket
 * "72xReceipt" size, which is really 80mm wide paper w/72mm wide print region
 *
 * SPDX-License-Identifier: MIT
 */

namespace OpenTHC\POS\PDF;

class PickTicket extends \OpenTHC\POS\PDF\Base\Receipt
{
	/**
	 *
	 */
	function setData($d)
	{
		$this->_b2c_sale = $d;
		$this->_doc_title = sprintf('Pick Ticket #%s', substr($d->id, -8));
		$this->_item_list = (array)$d->item_list;
		$this->setTitle($this->_doc_title);
	}

	/**
	 * License Name
	 * "Pick Ticket"
	 * Order ID (or part of it)
	 * Date Time in Human Format
	 */
	function drawHead()
	{
		$y = $this->getY();
		$this->drawBanner();

		// Reset Font
		$this->setFont('freesans', '', 14);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setTextColor(0x00, 0x00, 0x00);
		$this->cell($this->_width_view, 4, $this->_doc_title, null, null, 'C');

		// Date/Time
		$y = $this->getY() + 8;
		$dtC = new \DateTime();
		if ( ! empty($this->Company['tz'])) {
			$dtC->setTimezone(new \DateTimezone($this->Company['tz']));
		}
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, $dtC->format('Y-m-d H:i'), 0, null, 'C');

		// Item Count
		$y = $this->getY() + 8;
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, sprintf('Item Count: %d', $this->_b2c_sale->item_count), 0, null, 'C');

		// Unit Count
		$y = $this->getY() + 8;
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, sprintf('Unit Count: %d', $this->_b2c_sale->unit_count), 0, null, 'C');

		$y = $this->getY() + 8;
		$this->drawLine($y);
		$y += 2;

		// $this->head_text = "HEAD TEXT\n----";
		if ( ! empty($this->head_text)) {
			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 10);
			$this->multicell($this->_width_view, 5, $this->head_text, null, 'C', null, 1);
			$y = $this->getY();
			// $y += 6;
			$this->drawLine($y);
		}

		// $y += 6;
		// $this->my_line($y);
		$this->setY($y);

	}

	/**
	 *
	 */
	function _renderPrintable()
	{
		$this->drawHead();

		$this->setFont('freesans', '', 12);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setTextColor(0x00, 0x00, 0x00);

		$y = $this->getY();

		$idx = 0;
		foreach ($this->_item_list as $li_ulid => $li_data) {

			$idx++;

			if (is_array($li_data)) {
				$li_data = (object)$li_data;
			}

			// $txt = trim(sprintf('%s %s', $SI['Product']['name'], $SI['Variety']['name']));
			$txt = sprintf('#%d %s', $idx, $li_data->name);
			// $this->colLeft($y, $txt);
			$this->setXY($this->_init_x, $y);
			$this->multicell($this->_width_view, 4, $txt, 0, 'L');

			$y = $this->getY();
			$y += 1;
			// $txt = sprintf('%d @ $%s', $SI['unit_count'], number_format($SI['base_price'], 2));
			// $txt = $li_data->name;
			// $this->setXY($this->_init_x, $y);
			// $this->multicell($this->_width_view, 4, $txt, 0, 'L');

			// $y = $this->getY();
			$this->colLeft($y, sprintf('Pick: %d @ $%0.2f', $li_data->unit_count, $li_data->unit_price));
			if (empty($li_data->full_price)) {
				// throw ...
				$li_data->full_price = $li_data->unit_count * $li_data->unit_price;
			}
			$this->colRight($y, sprintf('$%0.2f', $li_data->full_price, 2));
			// $this->setXY($this->_init_x, $y);
			// $this->cell($this->_width_view, 4, $txt, 0, 0, 'R');

			// Item Taxes
			// Do we have These available?
			// if ($SI['base_price'] != $SI['full_price']) {
			// 	$y += 6;
			// 	$this->colLeft($y, '+ Taxes & Adjustments');
			// 	$this->colRight($y, number_format($SI['full_price'] - $SI['base_price'], 2));
			// }

			$y += 8;

		}

		$y += 8;

		$this->setY($y);

		// Summary ID
		$y = $this->getY();
		// $y += 4;
		$this->setXY($this->_init_x, $y);
		$this->setFont('freesans', '', 10);
		$this->cell($this->_width_view, 4, sprintf('PT:%s', \Edoceo\Radix\ULID::create()), null, 1, 'C');
		// $this->drawLine($y);

		$y = $this->getY();
		$w = $this->getLineWidth();
		$this->setLineWidth(0.40);
		$this->drawLine($y);
		$y += 1;
		$this->drawLine($y);

	}

}
