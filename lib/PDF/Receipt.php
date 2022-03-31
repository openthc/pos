<?php
/**
 * PDF Receipt
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\PDF;

class Receipt extends \App\PDF\Base
{
	protected $Company;
	protected $License;

	private $_b2c_sale;
	private $_item_list = [];

	function __construct($orientation='P', $unit='mm', $format=array(72, 1000), $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->setAutoPageBreak(false);
	}

	function setCompany($x)
	{
		$this->Company = $x;
	}

	function setLicense($x)
	{
		$this->License = $x;
	}

	function setSale($s)
	{
		$this->_b2c_sale = $s;
		$this->setTitle(sprintf('Receipt #%s', $this->_b2c_sale['id']));
	}

	function setItems($b2c_item_list)
	{
		$this->_item_list = $b2c_item_list;
	}

	function drawHead()
	{
		$this->setXY(0, 5);
		$this->setFont('freesans', 'B', 18);
		$this->setFillColor(0x10, 0x10, 0x10);
		$this->setTextColor(0xff, 0xff, 0xff);
		$this->cell(72, 4, $_SESSION['Company']['name'], null, null, 'C', true);
	}

	function drawSummary()
	{
		$y = $this->getY();

		$y+= 4;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Subtotal:');
		$this->setXY(36, $y);
		$this->cell(35, 5, '$-.--', 0, 0, 'R');

		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Discount:');
		$this->setXY(36, $y);
		$this->cell(35, 5, '$-.--', 0, 0, 'R');

		// Tax A
		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Cannabis Tax (Included):');
		$this->setXY(36, $y);
		$this->cell(35, 5, '$-.--', 0, 0, 'R');

		// Tax B
		// $y+= 5;
		// $this->setXY(1, $y);
		// $this->cell(70, 5, 'Excise Tax:');

		// Tax C
		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Sales Tax (Included):');
		$this->setXY(36, $y);
		$this->cell(35, 5, '$-.--', 0, 0, 'R');

		$y += 6;
		$this->line(1, $y, 71, $y);

		$y += 2;
		$this->setFont('', 'B');
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Total:');
		$this->setXY(36, $y);
		$this->cell(35, 5, '$-.--', 0, 0, 'R');
		$this->setFont('', '');

		$y += 7;
		$this->line(1, $y, 71, $y);

		// Cash Paid
		$y += 2;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Cash Paid:');

		// Change
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Change:');

		// Receipt ID
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'TXN:');
		$this->setXY(36, $y);
		$this->cell(35, 5, substr($this->_b2c_sale['id'], 0, 16), 0, 0, 'R');

		// Transaction Type
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'TXN Type:');
		$this->setXY(36, $y);
		$this->cell(35, 5, 'SALE', 0, 0, 'R');

		// Register / Till Info
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'REG:');
		$this->setXY(36, $y);
		$this->cell(35, 5, $this->_b2c_sale['terminal_id'], 0, 0, 'R');

		// Date/Time
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'DTS:');
		$this->setXY(36, $y);
		$this->cell(35, 5, _date('Y-m-d H:i', $this->_b2c_sale['created_at'], 'America/Los_Angeles'), 0, 0, 'R');

	}

	function drawTail()
	{
		$y = $this->getY();
		$y += 10;

		// Line
		$this->line(1, $y, 71, $y);
		$y += 1;

		$tail = $this->loadTailText();

		$y += 6;
		$this->setXY(1, $y);
		$this->setFont('freesans', '', 10);
		$this->multicell(70, 5, $tail, null, 'C', null, 1);

	}

	function drawFoot()
	{
		// FOOT
		$foot = $this->loadFootText();

		$y = $this->getY();
		$y += 1;
		$this->line(1, $y, 71, $y);
		$y += 1;

		$this->setXY(1, $y);
		$this->setFont('freesans', '', 10);
		$this->multicell(70, 5, $foot, null, 'L', null, 1);

		// If FeedBack Link
		if (true) {

			$y = $this->getY();

			$link = sprintf('https://%s/feedback/%s', $_SERVER['SERVER_NAME'], $this->_b2c_sale['id']);

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

		}

	}

	/**
	 *
	 */
	function render()
	{
		$this->addPage('P', [ 72, 5000 ]);

		// First render to discover height
		$this->_renderPrintable();
		$y = $this->getY();
		$y = ceil($y + 5);

		// Clear and render correct height
		$this->deletePage(1);
		$this->addPage('P', [ 72, $y ]);
		$this->_renderPrintable();
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
		$y = 20;
		foreach ($this->_item_list as $SI) {

			$this->setXY(1, $y);
			$this->cell(70, 5, $SI['Product']['name'] . ' ' . $SI['Variety']['name']);

			$y += 5;
			$this->setXY(1, $y);
			$this->cell(70, 5, rtrim($SI['unit_count'], '0.')  . ' x $' . number_format($SI['unit_price'], 2), null, null, 'R');

			$y += 5;
		}
		$this->setY($y);

		$this->drawSummary();
		$this->drawTail();
		$this->drawFoot();

	}

	/**
	 *
	 */
	function loadHeadText()
	{
		return $this->_load_text_from('pos-receipt-head', 'receipt-head.txt');
	}

	/**
	 *
	 */
	function loadFootText()
	{
		return $this->_load_text_from('pos-receipt-foot', 'receipt-foot.txt');
	}

	/**
	 *
	 */
	function loadTailText()
	{
		return $this->_load_text_from('pos-receipt-tail', 'receipt-tail.txt');
	}

	/**
	 * @param $d Database Key
	 * @param $f File Name
	 */
	private function _load_text_from($d, $f)
	{
		$text = $this->Company->opt($d);

		if (empty($text)) {
			$file = sprintf('%s/etc/%s', APP_ROOT, $f);
			if (is_file($file)) {
				$text = file_get_contents($file);
			}
		}

		return $text;

	}

}
