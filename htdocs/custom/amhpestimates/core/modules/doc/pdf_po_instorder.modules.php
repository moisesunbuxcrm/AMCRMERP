<?php
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/core/modules/modules_po.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';
require_once TCPDF_PATH.'tcpdf.php';
require_once 'pdf_po_base.php';

/**
 *	Class to generate an installation PDF for a Production Order
 */
class pdf_po_instorder extends pdf_po_base
{
	var $po;

	function __construct($db, $po=null)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->po = $po;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = "po_instorder";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_po_instorder_impl($this->db, $this->po, $outputlangs);
	}

	function setFilename()
	{
		global $conf;

		// Definition of $dir and $file
		if ($this->po->specimen)
		{
			$this->dir = $conf->amhpestimates->dir_output;
			$this->filename = "SPECIMEN.pdf";
			$this->filepath = $this->dir . "/" . $this->filename;
		}
		else
		{
			$ref = dol_sanitizeFileName($this->po->PONUMBER);
			$this->dir = $conf->amhpestimates->dir_output . "/invoice-" . $ref;
			$this->filename = "instorder-" . $ref . ".pdf";
			$this->filepath = $this->dir . "/" . $this->filename;
		}
	}
}

class pdf_po_instorder_impl extends pdf_po_base_impl 
{
	var $po;
	var $invoiceNumberR, $summaryR, $validityR;

	function __construct($db, $po, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->po = $po;

		$this->SetTitle($this->outputlangs->convToOutputCharset("Installation Order ".$po->PONUMBER));
		$this->SetSubject($this->outputlangs->transnoentities("PdfCommercialProposalTitle"));
		$this->subtitle = "Installation Order";
	}

	function generate()
	{
		$this->AddPage('','',true); // Adds first page with header and footer automatically

		$midpointx = $this->getPageWidth()/2;
		$midpoint_margin = 2;
		$column_width = $this->pageR->w-$midpointx-$midpoint_margin/2;
		$right_col_start = $midpointx+$midpoint_margin/2;
		
		$this->AddCustomerInfo($this->po, $this->pageR->x, $this->headerR->GetBottom(), $column_width, 0);
		$this->AddCustomerInfoPhones($this->po, $right_col_start, $this->headerR->GetBottom(), $column_width, 0);
		$customerBottom = max($this->customerInfoR->GetBottom(), $this->customerInfoPhonesR->GetBottom());
		$this->AddContactInfo($this->po, $this->pageR->x, $customerBottom, $column_width, 0);
		$this->AddContactInfoPhones($this->po, $right_col_start, $customerBottom+8, $column_width, 0);
		$contactBottom = max($this->contactInfoR->GetBottom(), $this->contactInfoPhonesR->GetBottom());
		$this->AddObservation($this->po->OBSINST, $this->pageR->x, $contactBottom, $this->pageR->w/2-8, 0);
		$this->AddVendorInfo($right_col_start, $this->customerInfoPhonesR->GetBottom()+4, $column_width, 0);

		$this->AddSummary($this->pageR->x+$this->pageR->w/2, $this->observationR->GetBottom(), $this->GetFontSize(), $this->pageR->w/2, 0);

		$itemHeaders = array(
			array('prop'=>'LineNumber','width'=>.066),
			array('prop'=>'MATERIAL','width'=>.13),
			array('prop'=>'PRODUCTTYPENAME','width'=>.13),
			array('prop'=>'COLOR','width'=>.13),
			array('prop'=>'WINDOWSTYPE','width'=>.13),
			array('prop'=>'OPENINGW','width'=>.066, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGHT','width'=>.066, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'ALUMINST','width'=>.066, 'format'=>function($val) { return round($val, 2); }),
			array('prop'=>'TRACK','width'=>.066, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGHT4','width'=>.066, 'format'=>function($val) { return round($val, 2); }),
			array('prop'=>'ALUMINST4','width'=>.066, 'format'=>function($val) { return round($val, 2); })
		);
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->AddItemsArea($this->po, $itemHeaders, $this->pageR->x,max($this->summaryR->GetBottom(),$this->observationR->GetBottom())+$this->GetFontSize(), $this->pageR->w, 0);
		$this->Close();
	}

	//Page header
	public function Header() {
		parent::Header();

		$x = $this->headerR->x;
		$w = $this->headerR->w;
		$y = $this->headerR->GetBottom()-$this->getFontSizePt()/2;
		$this->SetXY($x,$y);

		$this->AddPONumberAndDate($x, $this->GetY(), $w, 0);
		$this->Ln($this->getFontSizePt()/2);
		$this->SetDrawColor(0,0,0);
		$this->Line($x, $this->getY(), $this->headerR->GetRight(), $this->getY());
		$this->Ln($this->getFontSizePt()/2);

		$this->headerR = new PDFRectangle($x, $y, $w, $this->getY() - $y); // Record dimensions of header for use by other elements
	}
	
	public function AddPONumberAndDate($x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$html = "<B>PO Number:</B> ".$this->po->PONUMBER."&nbsp;&nbsp;&nbsp;&nbsp;<B>Date:</B> _______________";
		$this->writeHTMLCell($w, 0, $x, $y, $html, 0, 1, false, true, 'C');
		$this->invoiceNumberR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}
	
	public function AddObservation($txt, $x, $y, $w, $h, $border=0)
	{
		$this->SetXY($x, $y);
		if ($txt)
			$this->writeHTMLCell($w, 0, $x, $y, "<B>Observation:</B>", 0, 1, false, true, 'L');
		parent::AddObservation($txt,$x,$this->GetY(),$w,$h,1);
	}

	public function AddItemsHeader($itemHeaders, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$headerHeight = $this->GetCellHeight($this->getFontSize()*1.05)*2;
		$c=0;
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Open.\nNo.", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nMaterial", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Product\nType", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nColor", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Window\nType", 1, 'C', false, 0);

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*2, $headerHeight/2, "Opening", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "W", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "HT", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nAlum", 1, 'C', false, 0);

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nTrack", 1, 'C', false, 0);

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nHT+", 1, 'C', false, 0);

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Total\nSq.Ft.", 1, 'C', false, 0);

		$this->Ln($headerHeight);

		$this->itemsHeaderR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}

	public function AddSummary($x, $y, $gap, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = ($w-$midpoint_margin)/2;
		
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Default Color:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->MultiCell($column_width, $h, strtoupper($this->po->COLOR), $this->draw_borders, 'L', false, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Total Square Feet:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->MultiCell($column_width, $h, round($this->po->TOTALALUMINST*1000)/1000, $this->draw_borders, 'L', false, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Square Feet Inst. Price:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->MultiCell($column_width, $h, number_format($this->po->SQINSTPRICE, 2), $this->draw_borders, 'L', false, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Inst. Price:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->MultiCell($column_width, $h, number_format($this->po->INSTSALESPRICE, 2), $this->draw_borders, 'L', false, 1);

		$this->summaryR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}
}
