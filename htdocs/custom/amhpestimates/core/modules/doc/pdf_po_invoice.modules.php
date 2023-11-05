<?php
//test de guardados
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
 *	Class to generate a invoice PDF for a Production Order
 */
class pdf_po_invoice extends pdf_po_base
{
	var $po;

	function __construct($db, $po=null)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->po = $po;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = "po_standard";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_po_invoice_impl($this->db, $this->po, $outputlangs);
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
			$this->filename = "invoice-" . $ref . ".pdf";
			$this->filepath = $this->dir . "/" . $this->filename;
		}
	}
}

class pdf_po_invoice_impl extends pdf_po_base_impl 
{
	var $po;
	var $invoiceNumberR, $summaryR, $validityR;

	function __construct($db, $po, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->po = $po;

		$this->SetTitle($this->outputlangs->convToOutputCharset("Invoice ".$po->PONUMBER));
		$this->SetSubject($this->outputlangs->transnoentities("PdfCommercialProposalTitle"));
		$this->subtitle = "Invoice";
	}

	function generate()
	{
		$this->AddPage('','',true); // Adds first page with header and footer automatically

		$midpointx = $this->getPageWidth()/2;
		$midpoint_margin = 10;
		$column_width = $this->pageR->w-$midpointx-$midpoint_margin/2;
		$right_col_start = $midpointx+$midpoint_margin/2;
		
		$this->AddInvoiceNumber($right_col_start, $this->headerR->GetBottom(), $column_width, 0);
		$this->AddColor($this->po, $this->invoiceNumberR->x, $this->invoiceNumberR->GetBottom()+3.5, $column_width, 0);
		$this->AddDocumentDate(date(DATE_W3C), $this->pageR->x, $this->headerR->GetBottom(), $column_width, 0);
		$this->AddCustomerInfo($this->po, $this->pageR->x, $this->documentDateR->GetBottom(), $column_width, 0);
		$this->AddCustomerInfoPhones($this->po, $right_col_start, $this->colorR->GetBottom(), $column_width, 0);
		$customerBottom = max($this->customerInfoR->GetBottom(), $this->customerInfoPhonesR->GetBottom());
		$this->AddContactInfo($this->po, $this->pageR->x, $customerBottom, $column_width, 0);
		$this->AddContactInfoPhones($this->po, $right_col_start, $customerBottom, $column_width, 0);
		$contactBottom = max($this->contactInfoR->GetBottom(), $this->contactInfoPhonesR->GetBottom());
		$this->AddVendorInfo($right_col_start, $contactBottom, $column_width, 0);

		$itemHeaders = array(
			array('prop'=>'LineNumber','width'=>.06),
			array('prop'=>'PRODUCTTYPENAME','width'=>.13),
			array('prop'=>'MATERIAL','width'=>.125),
			array('prop'=>'WINDOWSTYPE','width'=>.1),
			array('prop'=>'COLOR','width'=>.12),
			array('prop'=>'SQFEETPRICE','width'=>.06, 'format'=>function($val) { return round($val, 2); }),
			array('prop'=>'INSTFEE','width'=>.06, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGW','width'=>.0725, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGHT','width'=>.0725, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'EST8HT','width'=>.0725, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'TRACK','width'=>.0725, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'ALUM','width'=>.0725, 'format'=>function($val) { return round((float)$val, 2); })
		);
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->AddItemsArea($this->po, $itemHeaders, $this->pageR->x, $contactBottom+$this->GetFontSize(), $this->pageR->w, 0);
		$this->AddOfferings($this->po, $this->pageR->x, $this->lastItemR->GetBottom()+$this->GetFontSizePt()/2, $this->pageR->w, 0);

		$this->AddObservation($this->po->ESTOBSERVATION, $this->pageR->x, $this->offeringsR->GetBottom()+$this->GetFontSize(), $this->pageR->w, 0);
		$this->AddSummary($this->pageR->x + $this->pageR->w/2, $this->observationR->GetBottom(), $this->GetFontSize(), $this->pageR->w/2, 0);
		$this->AddValidityNote($this->pageR->x, $this->summaryR->GetBottom()+$this->GetFontSize(), $this->GetFontSize(), $this->pageR->w, 0);

		$this->Close();
	}

	public function AddInvoiceNumber($x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 8;
		$column_width = $w/2-$midpoint_margin/2;


$sql=  "SELECT value AS value FROM  ".MAIN_DB_PREFIX."const ";
        $sql.= " WHERE  name = 'main_info_siren' ";
        $resql = $this->db->query($sql);
        foreach($resql as $value){

            $val= $value["value"] ;

        }
		
		$sql2=  "SELECT soc.barcode FROM  ".MAIN_DB_PREFIX."ea_po as po ";
        $sql2.= " LEFT JOIN llx_societe AS soc ON soc.rowid = po.customerId ";
		$sql2.= " where po.poid =  " . $this->po->POID;
        $resql2 = $this->db->query($sql2);
        foreach($resql2 as $value2){

            $barcode = $value2["barcode"] ;

        }
		
		


		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		//$this->Cell($column_width, $h, "Invoice No.:", $this->draw_borders, 0, "R");
		 $this->SetX($x+18);
        $this->Cell($column_width, $h, "Invoice No.:", $this->draw_borders, 0, "L");
        $this->SetX($x+42);
		$this->Cell($column_width, $h, $this->po->PONUMBER, $this->draw_borders, 1, "L");
		$this->invoiceNumberR = new PDFRectangle($x, $y+4, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
        $this->writeHTMLCell($column_width, 0, $x+17, $y+4 , "License No.: ", 0, 1, false, true, 'L');
        $this->writeHTMLCell($column_width, 0, $x+42, $y+4 , $val, 0, 1, false, true, 'L');
		$this->writeHTMLCell($column_width, 0, $x+22, $y+8 , "Folio No.: ", 0, 1, false, true, 'L');
		$this->SetDefaultFont();
		$this->writeHTMLCell($column_width+10, 0, $x+42, $y+8 , $barcode,   0, 1, false, true, 'L');
	}
	
	public function AddItemsHeader($itemHeaders, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$headerHeight = $this->GetCellHeight($this->getFontSize()*1.05)*2;
		$c=0;
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Open.\nNo.", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Product\nType", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nMaterial", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Window\nType", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nColor", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Sq.Ft.\nPrice", 1, 'C', false, 0);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Inst.\nFee", 1, 'C', false, 0);

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w*2, $headerHeight/2, "Opening", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "W", 1, 0, 'C');
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "HT", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, " \nHT", 1, 'C', false, 0);

		$xx = $this->GetX();
		$this->Cell($itemHeaders[$c]['width']*$w, $headerHeight/2, "Track", 1, 0, 'C');
		$this->Ln($headerHeight/2);
		$this->setX($xx);
		$this->Cell($itemHeaders[$c++]['width']*$w, $headerHeight/2, "W", 1, 0, 'C');

		$this->setXY($this->GetX(), $y);
		$this->MultiCell($itemHeaders[$c++]['width']*$w, $headerHeight, "Total\nSq.Ft.", 1, 'C', false, 0);

		$this->Ln($headerHeight);

		$this->itemsHeaderR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}

	public function AddSummary($x, $y, $gap, $w, $h)
	{
		dol_syslog("AddSummary():position= ".$this->GetX().", ".$this->GetY());

		$this->SetXY($x, $y);
		if ($this->NeedsNewPage($gap + $this->getFontSize()*5))
		{
			$this->AddPage('','',true);
			$y = $this->headerR->GetBottom() + $this->getFontSizePt();
			$this->SetXY($x, $y);
		}
		else
		{
			$this->Ln($gap);
			$this->SetX($x);
		}

		$midpoint_margin = 8;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs)*2);
		$this->Cell($column_width, 0, "Total Amount:", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'',pdf_getPDFFontSize($this->outputlangs)*2);
		$this->Cell($column_width, 0, "$".number_format($this->po->SALESPRICE, 2), 0, 1, 'R');
		$this->SetX($x);

		$this->SetDefaultFont();
		$this->summaryR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}

	public function AddValidityNote($x, $y, $gap, $w, $h)
	{
		$this->SetXY($x, $y);
		if ($this->NeedsNewPage($gap + $this->getFontSize()*3))
		{
			$this->AddPage('','',true);
			$y = $this->headerR->GetBottom() + $this->getFontSizePt();
			$this->SetXY($x, $y);
		}
		else
		{
			$this->Ln($gap);
			$this->SetX($x);
		}

		$this->SetTextColor(235,46,65);
		$this->writeHTMLCell($this->pageR->w, 0, $x, $y, "There will be a 3% charge for using a credit card", 0, 1, false, true, 'C');
		$this->SetTextColor(0,0,0);

		$this->validityR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}
}
