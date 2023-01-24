<?php
/**
 * PDF Base Class
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace App\PDF;

class Base extends \TCPDF
{
	/**
	 * Default TCPDF Constructor
	 */
	function __construct($orientation='P', $unit='in', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

		// set document information
		$this->setAuthor('openthc.com');
		$this->setCreator('openthc.com');
		//$this->setTitle($name);
		//$this->setSubject($name);
		//$this->setKeywords($this->name);

		// set margins
		$this->setMargins(0, 0, 0, true);
		$this->setHeaderMargin(0);
		$this->setPrintHeader(false);
		$this->setFooterMargin(0);
		$this->setPrintFooter(false);

		// set auto page breaks
		$this->setAutoPageBreak(true, 1/2);

		// set image scale factor
		$this->setImageScale(0);

		// set default font subsetting mode
		$this->setFontSpacing(0);
		$this->setFontStretching(100);
		$this->setFontSubsetting(true);

		// Cells
		$this->setCellMargins(0, 0, 0, 0);
		$this->setCellPaddings(0, 0, 0, 0);

		// Set font
		$this->setFont('freesans', '', 14, '', true);

		// Set viewer preferences
		$arg = array(
			'HideToolbar' => true,
			'HideMenubar' => true,
			'HideWindowUI' => true,
			'FitWindow' => true,
			'CenterWindow' => true,
			'DisplayDocTitle' => true,
			'NonFullScreenPageMode' => 'UseNone', // UseNone, UseOutlines, UseThumbs, UseOC
			'ViewArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'ViewClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintScaling' => 'None', // None, AppDefault
			'Duplex' => 'Simplex', // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
			//'PickTrayByPDFSize' => true,
			//'PrintPageRange' => array(1,1,2,3),
			//'NumCopies' => 2
		);
		$this->setViewerPreferences($arg);
		$this->SetDisplayMode('fullwidth');

	}

}
