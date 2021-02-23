<?php
/**
 * PDF Receipt
 */

namespace App\PDF;

class Receipt extends \App\PDF\Base
{
	protected $Company;
	protected $License;

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
		// $c = count($b2c_item_list);
		// Try to Determine How tall to be?
	}

	function drawHead()
	{
		// $_SESSION['Company']['name'] = '$Company Name Here$';

		$this->setXY(0, 5);
		$this->setFont('Helvetica', 'B', 18);
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

		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Discount:');

		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Cannabis Tax (Included):');

		// $y+= 5;
		// $this->setXY(1, $y);
		// $this->cell(70, 5, 'Excise Tax:');

		$y+= 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Sales Tax (Included):');

		$y += 6;
		$this->line(1, $y, 71, $y);

		$y += 1;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Total:');

		$y += 6;
		$this->line(1, $y, 71, $y);

		// Cash Paid
		$y += 1;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Cash Paid:');

		// Change
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Change:');

		// Receipt ID
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Transaction ID:');
		$this->setXY(36, $y);
		$this->cell(35, 5, '#1234567890', 0, 0, 'R');

		// Transaction Type
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Transaction Type:');
		$this->setXY(36, $y);
		$this->cell(35, 5, 'SALE', 0, 0, 'R');

		// Register / Till Info
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Register:');
		$this->setXY(36, $y);
		$this->cell(35, 5, 'REG-ABCD1234', 0, 0, 'R');

		// Date/Time
		$y += 5;
		$this->setXY(1, $y);
		$this->cell(70, 5, 'Time:');
		$this->setXY(36, $y);
		$this->cell(35, 5, _date('Y-m-d H:i:s', time(), 'America/Los_Angeles'), 0, 0, 'R');

	}

	function drawTail()
	{
		$y = $this->getY();
		$y += 10;

		// Line
		$this->line(1, $y, 71, $y);
		$y += 1;

		// TAIL
		// New Company Object?
		// $tail = $this->Company->opt('pos-receipt-tail');
		if (empty($tail)) {
			$file = sprintf('%s/etc/receipt-tail.txt', APP_ROOT);
			if (is_file($file)) {
				$tail = file_get_contents($file);
			}
		}

		$y += 6;
		$this->setXY(1, $y);
		$this->setFont('Helvetica', '', 10);
		$this->multicell(70, 5, $tail, null, 'C', null, 1);

	}

	function drawFoot()
	{
		// FOOT
		// $foot = $this->Company->opt('pos-receipt-foot');
		if (empty($foot)) {
			$file = sprintf('%s/etc/receipt-foot.txt', APP_ROOT);
			if (is_file($file)) {
				$foot = file_get_contents($file);
			}
		}

		$y = $this->getY();
		$y += 1;
		$this->line(1, $y, 71, $y);
		$y += 1;

		$this->setXY(1, $y);
		$this->setFont('Helvetica', '', 10);
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

	function render()
	{
		$this->addPage('P', [ 72, 5000 ]);
		$this->_renderCalcHeight();
		$y = $this->getY();
		$y = ceil($y) + 5;
		// var_dump($y);
		// exit;

		$this->deletePage(1);
		$this->addPage('P', [ 72, $y ]);
		$this->_renderPrintable();
	}

	function _renderCalcHeight()
	{
		$x = $this->_renderPrintable();
	}


	function _renderPrintable()
	{
		$this->drawHead();

		$this->setFont('Helvetica', '', 12);
		$this->setFillColor(0xff, 0xff, 0xff);
		$this->setTextColor(0x00, 0x00, 0x00);

		$y = $this->getY();
		$y = 20;
		foreach ($this->_item_list as $SI) {

			$I = $SI['Inventory'];

			$this->setXY(1, $y);
			$this->cell(70, 5, $I['name'] . ' ' . $I['name_strain']);

			$y += 5;
			$this->setXY(1, $y);
			$this->cell(70, 5, rtrim($SI['qty'], '0.')  . ' x $' . number_format($SI['unit_price'], 2), null, null, 'R');

			$y += 5;
		}
		$this->setY($y);

		$this->drawSummary();
		$this->drawTail();
		$this->drawFoot();

	}



}
