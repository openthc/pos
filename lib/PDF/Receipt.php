<?php
/**
 * PDF Receipt
 * "72xReceipt" size, which is really 80mm wide paper w/72mm wide print region
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\PDF;

class Receipt extends \OpenTHC\POS\PDF\Base
{
	protected $Company;
	protected $License;

	protected $_width_full = 72;

	private $_b2c_sale;
	private $_item_list = [];

	private $_init_x = 2;
	private $_width_view = 68;
	private $_width_half = 34;

	public $head_text = '';
	public $foot_text = '';
	public $tail_text = '';
	public $link_text = '';

	/**
	 * Defaults
	 */
	function __construct($orientation='P', $unit='mm', $format=array(72, 1000), $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->setAutoPageBreak(false);
	}

	/**
	 *
	 */
	function setCompany($x)
	{
		$this->Company = $x;
	}

	/**
	 *
	 */
	function setLicense($x)
	{
		$this->License = $x;
	}

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
		// Black Banner
		$this->setFont('freesans', 'B', 18);
		$this->setFillColor(0x10, 0x10, 0x10);
		$this->setTextColor(0xff, 0xff, 0xff);
		$this->setXY($this->_init_x, 4);
		$this->cell($this->_width_view, 4, $this->License['name'], null, null, 'C', true);

		// Reset Font
		$this->setFont('freesans', '', 12);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setTextColor(0x00, 0x00, 0x00);

		$y = 12;
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, sprintf('B2C/%s', substr($this->_b2c_sale['id'], 0, 16)), null, null, 'C');

		// Date/Time
		$dtC = new \DateTime($this->_b2c_sale['created_at']);
		if ( ! empty($this->Company['tz'])) {
			$dtC->setTimezone(new \DateTimezone($this->Company['tz']));
		}

		$y += 6;
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_view, 4, $dtC->format('Y-m-d H:i'), 0, null, 'C');

		if ( ! empty($this->head_text)) {

			$y = $this->getY();
			$y += 8;

			// Line
			$this->line(0, $y, $this->_width_full, $y);
			$y += 2;

			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 10);
			$this->multicell($this->_width_view, 5, $this->head_text, null, 'C', null, 1);

			$y = $this->getY();
			$y += 2;

			$this->line(0, $y, $this->_width_full, $y);

			$this->setY($y);

		}

		// $this->lineExperiment();

	}

	/**
	 *
	 */
	function drawSummary()
	{
		$y = $this->getY();

		$y += 4;
		$this->line(0, $y, $this->_width_full, $y);

		$y += 2;
		$this->colLeft($y, 'Subtotal:');
		$this->colRight($y, number_format($this->b2c_item_total, 2));

		// if ($this->getAttribute('adj-total')) {
		// 	$y+= 5;
		// 	$this->setXY(0, $y);
		// 	$this->cell($this->_width_half, 4, 'Discount:');
		// 	$this->setXY($this->_width_half, $y);
		// 	$this->cell($this->_width_half, 4, '$-.--', 0, 0, 'R');
		// }

		// Tax A
		$y+= 6;
		$this->colLeft($y, 'Cannabis Tax (Included):');
		$this->colRight($y, number_format($this->b2c_tax0_total, 2));

		// Tax B
		// $y+= 5;
		// $this->setXY(1, $y);
		// $this->cell($this->_width_view, 5, 'Excise Tax:');

		// Tax C
		$y+= 6;
		$this->colLeft($y, 'Sales Tax (Included):');
		$this->colRight($y, number_format($this->b2c_tax1_total, 2));

		$y += 7;
		$this->line(0, $y, $this->_width_full, $y);

		$full_price = $this->b2c_item_total + $this->b2c_tax0_total + $this->b2c_tax1_total;

		$y += 2;
		$this->setFont('', 'B');
		$this->colLeft($y, 'Total:');
		$this->colRight($y, number_format($full_price, 2));
		$this->setFont('', '');

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
		$this->line(0, $y, $this->_width_full, $y);
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
			$this->line(0, $y, $this->_width_full, $y);
			$y += 2;

			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 10);
			$this->multicell($this->_width_view, 5, $this->foot_text, null, 'L', null, 1);

		}

		// If FeedBack Link
		// if (true) {
		if ( ! empty($this->foot_link)) {

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
		$this->addPage('P', [ $this->_width_full, 5000 ]);

		// First render to discover height
		$this->_renderPrintable();
		$y = $this->getY();
		$y = ceil($y + 5);

		// Clear and render correct height
		$this->deletePage(1);
		$this->addPage('P', [ $this->_width_full, $y ]);
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
		$y += 2;

		foreach ($this->_item_list as $SI) {

			$txt = trim(sprintf('%s %s', $SI['Product']['name'], $SI['Variety']['name']));
			$this->colLeft($y, $txt);

			$y += 6;
			$txt = rtrim($SI['unit_count'], '0.')  . ' @ $' . number_format($SI['unit_price'], 2);
			$this->colLeft($y, $txt);
			$this->colRight($y, number_format($SI['unit_count'] * $SI['unit_price'], 2));
			// $this->setXY($this->_init_x, $y);
			// $this->cell($this->_width_view, 4, $txt, 0, 0, 'R');

			$y += 6;

			// Item Taxes
		}
		$this->setY($y);

		$this->drawSummary();
		$this->drawTail();
		$this->drawFoot();

		// $y = $this->getY();
		// $y += 4;

		// $this->line(0, $y, $this->_width_full, $y);


	}

	function colLeft($y, $txt, $alignment='L')
	{
		$this->setXY($this->_init_x, $y);
		$this->cell($this->_width_half, 4, $txt, 0, 0, $alignment);
	}

	function colRight($y, $txt, $alignment='R')
	{
		$this->setXY($this->_width_half, $y);
		$this->cell($this->_width_half + 2, 4, $txt, 0, 0, $alignment);
	}

	function lineExperiment()
	{
		// Line Experiment
		$y = $this->getY();

		// $y += 2;
		// $this->line(0, $y, $this->_width_full, $y);

		// $y+= 2;
		// $this->line(0, $y, 80, $y);
		// $y+= 2;
		// $this->line(0, $y, 72, $y);
		// $y+= 2;
		// $this->line(0, $y, 68, $y);
		// $y+= 2;
		// $this->line(0, $y, 36, $y);

		// $y+= 2;
		// $this->line(1, $y, 80, $y);
		// $y+= 2;
		// $this->line(1, $y, 72, $y);
		// $y+= 2;
		// $this->line(1, $y, 68, $y);
		// $y+= 2;
		// $this->line(1, $y, 36, $y);

		// $y+= 2;
		// $this->line(2, $y, 80, $y);
		// $y+= 2;
		// $this->line(2, $y, 72, $y);
		$y+= 2;
		$this->line(2, $y, 70, $y);
		// $y+= 2;
		// $this->line(2, $y, 36, $y);

		$this->setY($y);

	}

}
