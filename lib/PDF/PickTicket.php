<?php
/**
 * PickTicket
 * "72xReceipt" size, which is really 80mm wide paper w/72mm wide print region
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\PDF;

class PickTicket extends \OpenTHC\POS\PDF\Base
{
	protected $Company;
	protected $License;


	private $_b2c_sale;
	private $_item_list = [];

	private $_init_x = 2;
	private $_width_full = 80;
	private $_width_view = 76;
	private $_width_half = 38;

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

	function my_line($y)
	{
		parent::line($this->_init_x, $y, $this->_width_view, $y);
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
		// $this->head_text = $this->Company->getOption(sprintf('/%s/receipt/head', $this->License['id']));
		// $this->foot_text = $this->Company->getOption(sprintf('/%s/receipt/foot', $this->License['id']));
		// $this->tail_text = $this->Company->getOption(sprintf('/%s/receipt/tail', $this->License['id']));
		// $this->foot_link = $this->Company->getOption(sprintf('/%s/receipt/link', $this->License['id']));
	}

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
		// Black Banner
		$this->setFont('freesans', 'B', 18);
		$this->setFillColor(0x10, 0x10, 0x10);
		$this->setTextColor(0xff, 0xff, 0xff);
		$this->setXY($this->_init_x, 0);
		// $this->cell($this->_width_view, 4, $this->License['name'], null, null, 'C', $fill=true);
		$this->multicell($this->_width_view, 4, $this->License['name'], null, 'C', $fill=true);

		$y = ceil($this->getY()) + 2;
		$this->setXY($this->_init_x, $y);

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
		$this->cell($this->_width_view, 4, sprintf('Item Count: %d', count($this->_item_list)), 0, null, 'C');


		$y = $this->getY() + 8;
		$this->my_line($y);
		$y += 2;

		// $this->head_text = "HEAD TEXT\n----";
		if ( ! empty($this->head_text)) {
			$this->setXY($this->_init_x, $y);
			$this->setFont('freesans', '', 10);
			$this->multicell($this->_width_view, 5, $this->head_text, null, 'C', null, 1);
			$y = $this->getY();
			// $y += 6;
			$this->my_line($y);
		}

		// $y += 6;
		// $this->my_line($y);
		$this->setY($y);

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

		/**
		 * Item # and Metrc Tag "#1 ABC123....N"
		 * Product Name
		 * Unit Weight  |  Unit Price
		 * ---
		 */

		$idx = 0;
		foreach ($this->_item_list as $li_ulid => $li_data) {

			$idx++;

			if (is_array($li_data)) {
				$li_data = (object)$li_data;
			}

			// $txt = trim(sprintf('%s %s', $SI['Product']['name'], $SI['Variety']['name']));
			$txt = sprintf('#%d %s', $idx, $li_data->id);
			// $this->colLeft($y, $txt);
			$this->setXY($this->_init_x, $y);
			$this->multicell($this->_width_view, 4, $txt, 0, 'L');

			$y = $this->getY();
			$y += 1;
			// $txt = sprintf('%d @ $%s', $SI['unit_count'], number_format($SI['base_price'], 2));
			$txt = $li_data->name;
			$this->setXY($this->_init_x, $y);
			$this->multicell($this->_width_view, 4, $txt, 0, 'L');

			$y = $this->getY();
			$this->colRight($y, sprintf('$%0.2f', $li_data->unit_price, 2));
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
		// $this->my_line($y);

		$y = $this->getY();
		$w = $this->getLineWidth();
		$this->setLineWidth(0.40);
		$this->my_line($y);
		$y += 1;
		$this->my_line($y);

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

}
