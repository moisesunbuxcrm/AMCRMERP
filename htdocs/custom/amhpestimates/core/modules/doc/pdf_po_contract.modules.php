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
 *	Class to generate a contract PDF for a Production Order
 */
class pdf_po_contract extends pdf_po_base
{
	var $po;

	function __construct($db, $po=null)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->po = $po;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = "po_contract";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_po_contract_impl($this->db, $this->po, $outputlangs);
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
			$this->dir = $conf->amhpestimates->dir_output . "/contract-" . $ref;
			$this->filename = "contract-" . $ref . ".pdf";
			$this->filepath = $this->dir . "/" . $this->filename;
		}
	}
}

class pdf_po_contract_impl extends pdf_po_base_impl 
{
	var $po;
	var $estimateNumberR, $itemsIntro;
	var $summaryR, $validityR, $conditionsR;
	var $signatureReqR, $signaturesR, $totalR;

	function __construct($db, $po, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->po = $po;

		$this->SetTitle($this->outputlangs->convToOutputCharset("Contract ".$po->PONUMBER));
		$this->SetSubject($this->outputlangs->transnoentities("PdfCommercialProposalTitle"));
		$this->subtitle = "Contract";
	}

	function generate()
	{
		$this->AddPage('','',true); // Adds first page with header and footer automatically

		$midpointx = $this->getPageWidth()/2;
		$midpoint_margin = 10;
		$column_width = $this->pageR->w-$midpointx-$midpoint_margin/2;
		$right_col_start = $midpointx+$midpoint_margin/2;
		
		

		
		$this->AddContractNumber($right_col_start, $this->headerR->GetBottom(), $column_width, 0);
		$this->AddColor($this->po, $right_col_start, $this->estimateNumberR->GetBottom()+3.5, $column_width, 0);
		$this->AddDocumentDate($this->po->QUOTEDATE, $this->pageR->x, $this->headerR->GetBottom(), $column_width, 0);
		$this->AddCustomerInfo($this->po, $this->pageR->x, $this->colorR->GetBottom(), $column_width, 0);
		$this->AddCustomerInfoPhones($this->po, $right_col_start, $this->colorR->GetBottom(), $column_width, 0);
		$customerBottom = max($this->customerInfoR->GetBottom(), $this->customerInfoPhonesR->GetBottom());
		$this->AddContactInfo($this->po, $this->pageR->x, $customerBottom, $column_width, 0);
		$this->AddContactInfoPhones($this->po, $right_col_start, $customerBottom-9, $column_width, 0);
		$contactBottom = max($this->contactInfoR->GetBottom(), $this->contactInfoPhonesR->GetBottom());
		$this->AddVendorInfo($right_col_start, $contactBottom-10, $column_width, 0);
		$this->AddItemsIntro($this->pageR->x, $this->vendorInfoR->GetBottom()+12+$this->GetFontSize(), $this->pageR->w, 0);

		$itemHeaders = array(
			array('prop'=>'LineNumber','width'=>.083),
			array('prop'=>'PRODUCTTYPENAME','width'=>.147),
			array('prop'=>'MATERIAL','width'=>.147),
			array('prop'=>'WINDOWSTYPE','width'=>.167),
			array('prop'=>'COLOR','width'=>.123),
			array('prop'=>'SQFEETPRICE','width'=>.083, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'INSTFEE','width'=>.083, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGW','width'=>.083, 'format'=>function($val) { return round((float)$val, 2); }),
			array('prop'=>'OPENINGHT','width'=>.083, 'format'=>function($val) { return round((float)$val, 2); }),
		);
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->AddItemsArea($this->po, $itemHeaders, $this->pageR->x, $this->itemsIntroR->GetBottom()+$this->GetFontSize(), $this->pageR->w, 0);
		$this->AddConditions($this->pageR->x, $this->lastItemR->GetBottom()+$this->GetFontSize(), $this->pageR->w, 0);
		$this->AddOfferings($this->po, $this->pageR->x, $this->conditionsR->GetBottom()+$this->GetFontSizePt()/2, $this->pageR->w, 0);

		$this->AddObservation($this->po->ESTOBSERVATION, $this->pageR->x, $this->offeringsR->GetBottom()+$this->GetFontSize(), $this->pageR->w, 0);
		$this->AddSignatureRequirement($this->pageR->x, $this->observationR->GetBottom()+$this->GetFontSize(), $this->pageR->w, 0);

		$this->AddTotal($this->pageR->x, $this->signatureReqR->GetBottom()+$this->GetFontSize(), $this->GetFontSize()/2, $this->pageR->w, 0);
		$this->AddValidityNote($this->pageR->x, $this->totalR->GetBottom()+$this->GetFontSize(), $this->GetFontSize(), $this->pageR->w, 0);
		$this->AddSignatures($this->pageR->x, $this->validityR->GetBottom()+$this->GetFontSize()*5, $this->pageR->w, 0);

		$this->Close();
	}

	public function AddContractNumber($x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
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
		 $this->SetX($x+16);
		$this->Cell($column_width, $h, "Contract No.:", $this->draw_borders, 0, "L");
		 $this->SetX($x+18);
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->po->PONUMBER, $this->draw_borders, 1);
		$this->estimateNumberR = new PDFRectangle($x, $y+4, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->writeHTMLCell($column_width, 0, $x+17, $y+4 , "License No.: ", 0, 1, false, true, 'L');
        $this->writeHTMLCell($column_width, 0, $x+42, $y+4 , $val, 0, 1, false, true, 'L');
		$this->writeHTMLCell($column_width, 0, $x+22, $y+8 , "Folio No.: ", 0, 1, false, true, 'L');
		$this->writeHTMLCell($column_width+70, 0, $x+42, $y+8 , $barcode,   0, 1, false, true, 'L');
		$this->SetDefaultFont();

	}
	
	public function AddItemsIntro($x, $y, $w, $h)
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

		$this->writeHTMLCell($this->pageR->w, 0, $x, $y, "We propose to furnish labor and materials to install indicated products in the following masonry openings:", 0, 1, false, true, 'L');

		$this->itemsIntroR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
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

		$this->Ln($headerHeight/2);

		$this->itemsHeaderR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}

	public function AddConditions($x, $y, $w, $h)
	{
		$this->SetXY($x, $y);

		if ($this->NeedsNewPage($this->getFontSize()*5*2))
		{
			$this->AddPage('','',true);
			$y = $this->headerR->GetBottom() + $this->getFontSizePt();
			$this->SetXY($x, $y);
		}

		$html = "";
		if ((float)$this->po->PERMIT === (float)0)
		{
			$html .= "The Owner will obtain all the required permits.";
		}
		else
		{
			$html .= $this->GetOrganizationName()." will process the permit for the Homeowner.";
		}
		$html .= "<br/>Tentative installation time is <b>".$this->po->INSTTIME."</b> week(s) from the permit approval from the city.";
		
		if ($this->po->Check50 != 1)
			$html .= "<br/>Payment shall be 50 % deposit upon acceptance of the contract  - 40 % upon installation and 10 % after inspection is passed</b>.";

		$this->writeHTMLCell($this->pageR->w, 0, $x, $y, $html, 0, 1, false, true, 'L');

		$this->conditionsR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}

	public function AddSummary($x, $y, $gap, $w, $h)
	{
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

		$cw = array(.35, .2, .25, .2);
		$c=0;
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Permit Processing (Non Taxable):", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->PERMIT, 2), 0, 0, 'R');
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Square Feet Price:", 0, 0, 'R');
		$this->SetDefaultFont();
		//$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->SQFEETPRICE, 2), 0, 1, 'R');
		$this->Cell($cw[$c++]*$w, 0, "???", 0, 1, 'R');

		$this->SetX($x);
		$c=0;
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Installation (Non Taxable):", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->CUSTOMIZE, 2), 0, 0, 'R');
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Customized Value:", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->CUSTVALUE, 2), 0, 1, 'R');

		$this->SetX($x);
		$c=0;
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Total Square Feet :", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, number_format($this->po->TOTALALUM, 3), 0, 0, 'R');
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Sales Tax (".$this->po->SALES_TAX."%):", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->SALESTAXAMOUNT, 2), 0, 1, 'R');

		$c=0;
		$this->SetX($x+$cw[$c++]*$w+$cw[$c++]*$w);
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->Cell($cw[$c++]*$w, 0, "Sales Price:", 0, 0, 'R');
		$this->SetDefaultFont();
		$this->Cell($cw[$c++]*$w, 0, "$".number_format($this->po->SALESPRICE, 2), 0, 1, 'R');

		$this->summaryR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}

	public function AddSignatureRequirement($x, $y, $w, $h)
	{
		$this->SetXY($x, $y);

		if ($this->po->SignatureReq)
		{
			if ($this->NeedsNewPage($this->getFontSize()))
			{
				$this->AddPage('','',true);
				$y = $this->headerR->GetBottom() + $this->getFontSizePt();
				$this->SetXY($x, $y);
			}

			$this->writeHTMLCell($this->pageR->w, 0, $x, $y, "Estimate is part of the Contract and a signature is required to approve the Contract", 0, 1, false, true, 'C');
			$this->signatureReqR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
		}
		else
			$this->signatureReqR = new PDFRectangle($x, $y, $w, 0); // Record dimensions for use by other elements
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
		$this->writeHTMLCell($this->pageR->w, 0, $x, $y, "There will be a 3% charge for using a credit card ", 0, 1, false, true, 'C');
		$this->SetTextColor(0,0,0);
        $this->writeHTMLCell($this->pageR->w, 0, $x, $y+7, "<br>In the event A&M Hurricane Protection must employ the services of an attorney to collect on any unpaid balance due from Customer, the Customer understands and agrees that Customer will be obliged to and shall pay for A&M Hurricane Protection’s attorney’s fees and court costs for all legal work performed including pre-suit collection efforts, lawsuit attorney fees, appellate court attorney fees, and post-judgement collection attorney fees.</b>", 0, 1, false, true, 'C');

		$this->validityR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}

	public function AddTotal($x, $y, $gap, $w, $h)
	{
		$this->SetXY($x, $y-10);
		if ($this->NeedsNewPage($gap + $this->getFontSize()*2))
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

		if ((float)$this->po->PERMIT === (float)0)
		{
			$this->writeHTMLCell($w, 0, $x, $this->GetY(), "The total amount for the openings is: <b>$".number_format($this->po->SALESPRICE, 2)."</b>", 0, 1, false, true, 'C');
		}
		else
		{
			$this->writeHTMLCell($w, 0, $x, $this->GetY()+9, "The total amount for the openings above including the processing fee is: <b>$".number_format($this->po->SALESPRICE, 2)."</b>", 0, 1, false, true, 'C');
			
			
		}
		$this->SetX($x);

		$this->totalR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}


	public function AddSignatures($x, $y, $w, $h)
	{
		$this->SetXY($x, $y);
		if ($this->NeedsNewPage($this->getFontSize()*8))
		{
			$this->AddPage('','',true);
			$y = $this->headerR->GetBottom() + $this->getFontSizePt();
			$this->SetXY($x, $y);
		}

		$midpoint_margin = 8;
		$column_width = $w/2-$midpoint_margin/2;
		
		
		
		
		$left = $x;
		$top = $y;
		$txt="Qualifier:";
		$txtWidth=$this->GetStringWidth($txt)*1.25;
		$this->writeHTMLCell($column_width, 0, $left, $top, "<b>".$txt."</b>", 'T', 0, false, true, 'L');
		$this->SetX($left+$txtWidth);
		$this->MultiCell($column_width-$txtWidth, 0, $this->GetCEO(), $this->draw_borders, 'L', false, 1);

		$left = $x+$column_width+$midpoint_margin;
		$txt="Accepted By:";
		$txtWidth=$this->GetStringWidth($txt)*1.25;
		$this->writeHTMLCell($column_width, 0, $left, $top, "<b>".$txt."</b>", 'T', 0, false, true, 'L');
		$this->SetX($left+$txtWidth);
		$this->MultiCell($column_width-$txtWidth, 0, $this->po->CUSTOMERNAME, $this->draw_borders, 'L', false, 1);

		$this->Ln($this->getFontSize()*6);
		$left = $x;
		$top = $this->GetY();
		$txt="Representative:";
		$txtWidth=$this->GetStringWidth($txt)*1.25;
		$this->writeHTMLCell($column_width, 0, $left, $top, "<b>".$txt."</b>", 'T', 0, false, true, 'L');
		$this->SetX($left+$txtWidth);
		$this->MultiCell($column_width-$txtWidth, 0, $this->po->Salesman, $this->draw_borders, 'L', false, 1);

		$this->signaturesR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}
}
