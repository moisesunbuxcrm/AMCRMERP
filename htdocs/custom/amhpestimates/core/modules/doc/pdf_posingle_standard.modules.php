<?php
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/core/modules/modules_po.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';
require_once TCPDF_PATH.'tcpdf.php';
require_once 'pdf_posummary_base.php';

/**
 *	Class to generate a standard PDF for a single Production Order
 */
class pdf_posingle_standard extends pdf_posummary_base
{
	var $po;

	function __construct($db, $po)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->po = $po;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = "posingle_standard";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_posingle_standard_impl($this->db, $this->po, $outputlangs);
	}

	function setFilename()
	{
		global $conf;

		// Definition of $dir and $file
		$ref = date("Y-m-d-H-i-s");
		$this->dir = $conf->amhpestimates->dir_output . "/posingle-" . $ref;
		$this->filename = "posingle-" . $ref . ".pdf";
		$this->filepath = $this->dir . "/" . $this->filename;
	}
}

class pdf_posingle_standard_impl extends pdf_posummary_base_impl 
{
	var $po;

	var $summaryDataBottom, $vendorInfoR, $poTotalsR;

	function __construct($db, $po, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->po = $po;

		$this->SetTitle($this->outputlangs->convToOutputCharset("Production Order Single"));
		$this->SetSubject($this->outputlangs->transnoentities("PdfCommercialProposalTitle"));
		$this->headerTitle = "Production Order";

		$left_margin=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$right_margin=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$top_margin = 0;//isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$bottom_margin =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;
		$this->SetMargins($left_margin, $top_margin, $right_margin);

		$this->pageR = new PDFRectangle(
			$left_margin, $top_margin, 
			$this->getPageWidth()-$left_margin-$right_margin, 
			$this->getPageHeight()-$top_margin-$bottom_margin);
	}

	public function getHeaderFontSize()
	{
		return $this->getFontSizePt()-2;
	}

	public function getImageTopMargin()
	{
		return 0;
	}

	function generate()
	{
		$this->AddPage('','',true); // Adds first page with header and footer automatically

		$this->SetXY($this->pageR->x,$this->headerR->GetBottom());
		//$this->Ln($this->getFontSizePt()/2);
		$this->SetDrawColor(0,0,0);
		$this->Line($x, $this->getY(), $this->pageR->GetRight(), $this->getY());
		$this->Ln($this->getFontSizePt()/2);

		$midpointx = $this->getPageWidth()/2;
		$midpoint_margin = 2;
		$column_width = $midpointx-$this->pageR->x-$midpoint_margin/2;
		$right_col_start = $midpointx+$midpoint_margin/2;

		$top = $this->GetY();
		$this->AddCustomerInfo($this->po, $this->pageR->x, $top, $column_width, 0);
		$this->AddCustomerInfoPhones($this->po, $right_col_start, $top, $column_width, 0);
		$this->AddVendorInfo($right_col_start, $this->customerInfoPhonesR->GetBottom()+7, $column_width, 0);
		$bottom = max($this->customerInfoR->GetBottom(), $this->vendorInfoR->GetBottom());
		$this->AddObservation($this->po->OBSERVATION, $this->pageR->x, $bottom, $column_width, 0);
		$this->AddPOTotals($right_col_start, $bottom, $column_width, 0);

		$bottom = max($this->poTotalsR->GetBottom(), $this->observationR->GetBottom());
		$itemHeaders = array(
			array('prop'=>'LineNumber','width'=>0.0345), // Width is 1/29c
			array('prop'=>'COLOR','width'=>.0690),
			array('prop'=>'OPENINGW','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGHT','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'MOUNT','width'=>.0345),
			array('prop'=>'BLADESLONG','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'BLADESQTY','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'BLADESSTACK','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'BLADESLONG','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }), // STARTERS
			array('prop'=>'BLADESLONG','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }), // CENTER MALE
			array('prop'=>'BLADESLONG','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }), // CENTER FEMALE
			array('prop'=>'UPPERSIZE','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'UPPERTYPE','width'=>.0345),
			array('prop'=>'LOWERSIZE','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'LOWERTYPE','width'=>.0345),
			array('prop'=>'ANGULARSIZE','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'ANGULARTYPE','width'=>.0345),
			array('prop'=>'ANGULARQTY','width'=>.0345),
			array('prop'=>'EXTRAANGULARSIZE','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'EXTRAANGULARTYPE','width'=>.0345),
			array('prop'=>'EXTRAANGULARQTY','width'=>.0345),
			array('prop'=>'TUBESIZE','width'=>.0345, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'TUBETYPE','width'=>.0345),
			array('prop'=>'TUBEQTY','width'=>.0345),
			array('prop'=>'LEFT','width'=>.0345),
			array('prop'=>'RIGHT','width'=>.0345),
			array('prop'=>'LOCKIN','width'=>.0690),
		);
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt()-3);
		$prev_cell_padding = $this->getCellPaddings();
		$this->setCellPadding(0);
		$this->AddItemsArea($this->po, $itemHeaders, $this->pageR->x,$bottom+$this->GetFontSize(), $this->pageR->w, 0, "ACCORDION");
		$this->cell_padding = $prev_cell_padding;
		$this->SetX($this->pageR->x);

		if ($this->NeedsNewPage($this->getFontSizePt()))
		{
			$this->AddPage('','',true);
			$this->SetXY($x,$this->headerR->GetBottom());
		}
		else
			$this->Ln($this->getFontSizePt());

		$colorsAndItems = $this->getAccordionItemsByColor();
		
		foreach($colorsAndItems as $color => $items)
		{
			$this->addSummaryTable($color, $items, $this->pageR->x, $this->GetY(), $this->pageR->w, 0, "ACCORDION");
			$this->Ln($this->getFontSizePt());
		}

		$this->Close();
	}

	public function getAccordionItemsByColor()
	{
		$colors = Array();
		if ($this->po->items)
		{
			foreach($this->po->items as $item)
			{
				if ($item->PRODUCTTYPENAME == "ACCORDION")
				{
					if (!$colors[$item->COLOR])
						$colors[$item->COLOR] = Array();
					$colors[$item->COLOR][] = $item;
				}
			}
		}
		return $colors;
	}

	public function addAdditionalHeaderInfo()
	{
		$fontSize = $this->getFontSizePt();
		$this->SetFont($this->getFontFamily(), 'B', $fontSize+2);
		$this->Cell($this->pageR->w, $fontSize, "Accordions", $this->draw_borders, 1, 'R', 0, '', 0, false, 'M', 'M');

		$this->AddPONumberAndDate($x, $this->GetY(), $w, 0);
	}

	
	public function AddPONumberAndDate($x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$html = "<B>Date:</B> ".dol_print_date(strtotime($this->po->PODATE),"%m/%d/%Y")."&nbsp;&nbsp;&nbsp;&nbsp;<B>PO Number:</B> ".$this->po->PONUMBER;
		$this->writeHTMLCell($w, 0, $x, $y, $html, 0, 1, false, true, 'R');
		$this->invoiceNumberR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}

	public function AddObservation($txt, $x, $y, $w, $h, $border=0)
	{
		$this->SetXY($x,$y);

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($w, $h, "Observation:", $this->draw_borders, 1, "L");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->observationR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements

		parent::AddObservation($txt,$x,$this->GetY(),$w,$h,1);
	}

	public function AddPOTotals($x, $y, $w, $h)
	{
		$midpoint_margin = 2;
		$column_width = $w/4-$midpoint_margin/2;

		$col1 = $x;
		$col2 = $col1+$column_width+$midpoint_margin;
		$col3 = $col2+$column_width+$midpoint_margin;
		$col4 = $col3+$column_width+$midpoint_margin;

		$this->SetXY($x,$y+7);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Tracks:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col2);
		$this->Cell($column_width, $h, number_format($this->getAccordionTOTALTRACK(), 3), $this->draw_borders, 0);

		$this->SetX($col3);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Fasteners:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col4);
		$this->Cell($column_width, $h, $this->getAccordionFASTENERS(), $this->draw_borders, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Tapcons:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col2);
		$this->Cell($column_width, $h, $this->getAccordionTAPCONS(), $this->draw_borders, 0);

		$this->SetX($col3);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Total Long:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col4);
		$this->Cell($column_width, $h, number_format($this->getAccordionTOTALLONG(),3), $this->draw_borders, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Total Sq. Feet:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col2);
		$this->Cell($column_width, $h, $this->getAccordionTOTALALUM(), $this->draw_borders, 0);

		$this->SetX($col3);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Linear Feet:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($col4);
		$this->Cell($column_width, $h, number_format($this->getAccordionTOTALLINEARFT(),3), $this->draw_borders, 1);

		$this->poTotalsR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}

	public function getAccordionTOTALTRACK()
	{
		$TOTALTRACK = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
					$TOTALTRACK += $item->TRACK;
		return round($TOTALTRACK/6,3);
	}

	public function getAccordionFASTENERS()
	{
		$FASTENERS = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
					$FASTENERS += $item->BLADESLONG;
		return round($FASTENERS/8,3);
	}

	public function getAccordionTAPCONS()
	{
		$SUM_TRACK = 0;
		$SUM_BLADESLONG = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
				{
					$SUM_BLADESLONG += $item->BLADESLONG;
					$SUM_TRACK += $item->TRACK;
				}
		return ceil($SUM_TRACK * 2 / 8) + ceil($SUM_BLADESLONG * 2 / 16);
	}

	public function getAccordionTOTALLONG()
	{
		$TOTALLONG = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
					$TOTALLONG += $item->BLADESLONG;
		return round($TOTALLONG/6,3);
	}

	public function getAccordionTOTALALUM()
	{
		$TOTALALUM = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
					$TOTALALUM += $item->ALUM;
		return round($TOTALALUM,3);
	}

	public function getAccordionTOTALLINEARFT()
	{
		$TOTALLINEARFT = 0;
		if ($this->po->items)
			foreach($this->po->items as $item)
				if ($item->PRODUCTTYPENAME == "ACCORDION")
					$TOTALLINEARFT += $item->LINEARFT;
		return round($TOTALLINEARFT,3);
	}
	
	public function AddItemsHeader($itemHeaders, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt()+1);
		$headerHeight = $this->GetCellHeight($this->getFontSize()*1.5)*2;
		$c=0;
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Open\nNo.", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Color", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*2, $headerHeight/2, "Opening", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "W", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "HT", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Mnt.", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*3, $headerHeight/2, "Blades", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Long", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Qty", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Stack", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Star-ters", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Cent.\nMale", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Cent.\nFem.", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*2, $headerHeight/2, "Upper Track", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Size", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Type", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*2, $headerHeight/2, "Lower Track", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Size", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Type", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*3, $headerHeight/2, "Angular", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Size", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Type", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Qty", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*3, $headerHeight/2, "Extra Ang", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Size", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Type", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Qty", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*3, $headerHeight/2, "Tubes", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Size", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Type", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "Qty", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Left", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Right", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Lock", 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight, 'M');

		$this->Ln($headerHeight);
		
		$this->itemsHeaderR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt()-1);
	}

	function extractProperty($data, $index, $sourceName, $propName)
	{
		$source = $data[$sourceName];
		$c = count($source);
		if ($c > $index)
		{ 
			$obj = $source[$index];
			$arr = (array) $obj;
			return $arr[$propName];
		}

		return null;
	}
}
