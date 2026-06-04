<?php
/**
 * PDF Receipt
 * "72xReceipt" size, which is really 80mm wide paper w/72mm wide print region
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\PDF;

class Receipt extends \OpenTHC\POS\PDF\Base\Receipt
{
	/**
	 *
	 */
	function setSale($s)
	{
		$this->_b2c_sale = $s;
		$this->setTitle(sprintf('Receipt #%s', $this->_b2c_sale['id']));
	}

	/**
	 *
	 */
	function setItems($b2c_item_list)
	{
		$this->_item_list = $b2c_item_list;
	}

	/**
	 *
	 */
	function drawHead()
	{
		// Thi is zero but the banner for the Receipt starts lower than the one for PickTicket. Why?
		$y = $this->getY();
		$this->drawBanner();

		// Reset Font
		$this->setFont('freesans', '', 12);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setTextColor(0x00, 0x00, 0x00);
		$this->cell($this->_width_view, 4, sprintf('B2C/%s', substr($this->_b2c_sale['id'], 0, 16)), null, null, 'C');

		// Date/Time
		$dtC = new \DateTime($this->_b2c_sale['created_at']);
		if ( ! empty($this->Company['tz'])) {
			$dtC->setTimezone(new \DateTimezone($this->Company['tz']));
		}

		$y = $this->getY();
		$y += 6;
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, $dtC->format('Y-m-d H:i'), 0, null, 'C');

		if ( ! empty($this->head_text)) {

			$y = $this->getY();
			$y += 8;

			// Line
			$this->drawLine($y);
			$y += 2;

			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 12);
			$this->multicell($this->_width_view, 5, $this->head_text, null, 'C', null, 1);

			$y = $this->getY();
			$y += 2;

		}

		$y += 6;
		$this->drawLine($y);
		$this->setY($y);

		// $this->lineExperiment();

	}

	/**
	 *
	 */
	function drawSummary()
	{
		$y = $this->getY();

		$y += 4;
		$this->drawLine($y);

		$y += 2;
		$this->colLeft($y, 'Subtotal:');
		$this->colRight($y, number_format($this->_b2c_sale['base_price'], 2));

		// if ($this->getAttribute('adj-total')) {
		// 	$y+= 5;
		// 	$this->setXY(0, $y);
		// 	$this->cell($this->_width_half, 4, 'Discount:');
		// 	$this->setXY($this->_width_half, $y);
		// 	$this->cell($this->_width_half, 4, '$-.--', 0, 0, 'R');
		// }

		// $this->tax_info->tax_list->{'010PENTHC00BIPA0SST03Q484J'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0SST03Q484J', $License['id']))); // State
		// $this->tax_info->tax_list->{'010PENTHC00BIPA0C0T620S2M2'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0C0T620S2M2', $License['id']))); // County
		// $this->tax_info->tax_list->{'010PENTHC00BIPA0CIT5H9S6T3'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0CIT5H9S6T3', $License['id']))); // City
		// $this->tax_info->tax_list->{'010PENTHC00BIPA0MUT0FEEGCF'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0MUT0FEEGCF', $License['id']))); // Regional
		// $this->tax_info->tax_list->{'010PENTHC00BIPA0ET0FNBCKMH'} = floatval($Company->getOption(sprintf('/%s/b2c-item-price-adjust/010PENTHC00BIPA0ET0FNBCKMH', $License['id']))); // Excise


		// Tax A
		$y+= 6;
		$this->colLeft($y, 'Taxes & Adjustments:');
		$this->colRight($y, number_format($this->_b2c_sale['full_price'] - $this->_b2c_sale['base_price'], 2));

		// Tax B
		// $y+= 5;
		// $this->setXY(1, $y);
		// $this->cell($this->_width_view, 5, 'Excise Tax:');

		// Tax C
		// $y+= 6;
		// $this->colLeft($y, 'Sales Tax (Included):');
		// $this->colRight($y, number_format($this->b2c_tax1_total, 2));

		$y += 7;
		$this->drawLine($y);

		$y += 2;
		$this->setFont('freesans', 'B', 12);
		$this->colLeft($y, 'Total:');
		$this->colRight($y, number_format($this->_b2c_sale['full_price'], 2));
		$this->setFont('freesans', '', 12);

		$y += 7;
		$this->setY($y);

		// Cash Paid
		// $y += 2;
		// $this->colLeft($y, 'Cash Paid:');
		// $this->colRight($y, number_format(123.45, 2));

		// Change
		// $y += 5;
		// $this->colLeft($y, 'Change:');
		// $this->colRight($y, number_format(99.99, 2));

		// // Register / Till Info
		// $y += 5;
		// $this->setXY($this->_init_x, $y);
		// $this->cell($this->_width_half, 5, 'REG:');
		// $this->setXY($this->_width_half, $y);
		// $this->cell($this->_width_half, 5, $this->_b2c_sale['terminal_id'], 0, 0, 'R');

	}

	/**
	 *
	 */
	function drawTail() : void
	{
		if (empty($this->tail_text)) {
			return;
		}

		$y = $this->getY();
		$this->drawLine($y);
		$y += 2;

		$this->setXY($this->_init_x, $y);
		$this->setFont('freesans', '', 10);
		$this->multicell($this->_width_view, 5, $this->tail_text, null, 'C', null, 1);

		// Line
		$y = $this->getY();
		$y += 2;
		$this->setY($y);

	}

	/**
	 *
	 */
	function drawFoot()
	{
		// FOOT
		if ( ! empty($this->foot_text)) {

			$y = $this->getY();
			// $y += 2;
			$this->drawLine($y);
			$y += 2;

			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 10);
			$this->multicell($this->_width_view, 5, $this->foot_text, null, 'C', null, 1);

		}

		// If FeedBack Link
		// if (true) {
		if ( ! empty($this->foot_link)) {

			$y = $this->getY();
			$y += 2;
			$this->drawLine($y);

			$link = sprintf('%s/feedback/%s', OPENTHC_SERVICE_ORIGIN, $this->_b2c_sale['id']);

			$style = array(
				'border' => false,
				'padding' => 0,
				'hpadding' => 0,
				'vpadding' => 0,
				'fgcolor' => array(0x00, 0x00, 0x00),
				'bgcolor' => null,
				'position' => null,
			);
			$align = 'N';
			$distort = false;
			$x = 23;
			$y = $y + 4;
			$w = 26;
			$h = 26;

			$this->write2DBarcode($link, 'QRCODE,L', $x, $y, $w, $h, $style, $align, $distort);

			$y = $this->getY();
			$this->setY($y + 4);
		}

	}

	/**
	 *
	 */
	function _renderPrintable()
	{
		$this->drawHead();

		$this->setFont('freesans', '', 12);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setLineWidth(0.20);
		$this->setTextColor(0x00, 0x00, 0x00);

		$y = $this->getY();

		foreach ($this->_item_list as $SI) {

			$y += 8;

			$txt = trim(sprintf('%s %s', $SI['Product']['name'], $SI['Variety']['name']));
			$this->colLeft($y, $txt);

			$y += 6;
			$txt = sprintf('%d @ $%s', $SI['unit_count'], number_format($SI['base_price'], 2));
			$this->colLeft($y, $txt);
			$this->colRight($y, number_format($SI['base_price'], 2));
			// $this->setXY($this->_init_x, $y);
			// $this->cell($this->_width_view, 4, $txt, 0, 0, 'R');

			// Item Taxes
			// Do we have These available?
			if ($SI['base_price'] != $SI['full_price']) {
				$y += 6;
				$this->colLeft($y, '+ Taxes & Adjustments');
				$this->colRight($y, number_format($SI['full_price'] - $SI['base_price'], 2));
			}

		}

		$y += 8;

		$this->setY($y);

		$this->drawSummary();
		$this->drawFoot();
		$this->drawTail();

		$y = $this->getY();
		$w = $this->getLineWidth();
		$this->setLineWidth(0.40);
		$this->drawLine($y);
		$y += 1;
		$this->drawLine($y);

		// $y = $this->getY();
		// $y += 4;

		// $this->drawLine($y);

	}

}
