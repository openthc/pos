<?php
/**
 * Receipt
 * "72xReceipt" size, which is really 80mm wide paper w/72mm wide print region
 *
 * SPDX-License-Identifier: MIT
 */

namespace OpenTHC\POS\PDF\Base;

class Receipt extends \OpenTHC\POS\PDF\Base
{
	protected $_b2c_sale;
	protected $_item_list = [];

	protected $_init_x = 2;
	protected $_width_full = 80;
	protected $_width_view = 76;
	protected $_width_half = 38;

	public $head_text = '';
	public $foot_text = '';
	public $tail_text = '';
	public $link_text = '';

	/**
	 * Defaults
	 */
	function __construct($orientation='P', $unit='mm', $format=array(80, 1000), $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->setAutoPageBreak(false);
	}

	function drawBanner()
	{
		// Black Banner
		$this->setFont('freesans', 'B', 18);
		$this->setFillColor(0x10, 0x10, 0x10);
		$this->setTextColor(0xff, 0xff, 0xff);
		$this->setXY($this->_init_x, 0);
		// $this->cell($this->_width_view, 4, $this->License['name'], null, null, 'C', $fill=true);
		$this->multicell($this->_width_view, 4, $this->License['name'], null, 'C', $fill=true);

		$y = ceil($this->getY()) + 4; // + 4 was used on receipt -- looks ince
		$this->setXY($this->_init_x, $y);

	}

	function drawLine($y)
	{
		parent::line($this->_init_x, $y, $this->_width_view, $y);
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
		$y = max($y, 80);

		// Clear and render correct height
		$this->deletePage(1);
		$this->addPage('P', [ $this->_width_full, $y ]);
		$this->_renderPrintable();
	}

}
