<?php
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/core/modules/modules_po.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once TCPDF_PATH.'tcpdf.php';
require_once 'pdf_base.php';

/**
 *	Class to generate the basic elements of all AMHP PDF documents for estimates and PO's
 */
class pdf_po_base extends pdf_base
{
	function __construct($db)
	{
		parent::__construct($db);
		$this->name = "pdf_po_base";
	}
}

class pdf_po_base_impl extends pdf_base_impl 
{
	var $subtitle = "";
	var $headerR, $footerR;
	var $documentDateR, $colorR;
	var $offeringsR;

	var $headerIncludeNote, $headerIncludeAddress, $headerIncludePhones;

	function __construct($db, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->headerIncludeNote = true;
		$this->headerIncludeAddress = true;
		$this->headerIncludePhones = true;
	}

	//Page header
    public function Header() {
		global $conf;

		$this->SetDefaultDrawingColor();
		$x = $this->pageR->x;
		$w = $this->pageR->w;
		$y = $this->pageR->y;

		$this->SetXY($x,$y);
		
		$fontSize = $this->getFontSizePt();
		$this->SetFont($this->getFontFamily(), 'BU', $fontSize+2);
		$this->Cell(0, 14, $this->GetOrganizationName(), $this->draw_borders, 1, 'C', 0, '', 0, false, 'M', 'M');

		if ($this->subtitle != "")
		{
	        $this->SetFont($this->getFontFamily(), '', $fontSize+2);
			$this->Cell(0, 14, $this->subtitle, $this->draw_borders, 1, 'C', 0, '', 0, false, 'M', 'M');
		}

		if ($this->headerIncludeNote && $this->GetOrganizationNote() != "")
		{
	        $this->SetFont($this->getFontFamily(), 'B', $fontSize-3);
			$this->Cell(0, 9, $this->GetOrganizationNote(), $this->draw_borders, 1, 'C', 0, '', 0, false, 'M', 'M');
		}

		if ($this->headerIncludeAddress && $this->GetOrganizationAddress() != "")
		{
			$this->SetFont($this->getFontFamily(), '', $fontSize-4);
			$this->Cell(0, 8, $this->GetOrganizationAddress(), $this->draw_borders, 1, 'C', 0, '', 0, false, 'M', 'M');
		}

		if ($this->headerIncludePhones)
		{
			$phone = "<B>Phone:</B> ".$this->format_telephone($this->GetOrganizationPhone());
			$gap = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$fax = "<B>Fax:</B> ".$this->format_telephone($this->GetOrganizationFax());
			$this->writeHTMLCell(0, 8, $this->GetX(), $this->GetY(), $phone.$gap.$fax, $this->draw_borders, 1, false, true, "C", true);
		}

		$this->addAdditionalHeaderInfo();
		
		$this->headerR = new PDFRectangle($x, $y+$this->getFontSize(), $w, $this->getY() - $y); // Record dimensions of header for use by other elements

        $image_file = $conf->mycompany->dir_output."/logos/".$conf->global->MAIN_INFO_SOCIETE_LOGO;
		$this->Image($image_file, 10, 10, 0, $this->headerR->h, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetDefaultFont();
	}
	
	public function addAdditionalHeaderInfo()
	{

	}

    // Page footer
    public function Footer() {
		$x = $this->pageR->x;
		$w = $this->pageR->w;
		$y = $this->pageR->GetBottom()-$this->getFontSizePt()/2;

		$this->SetXY($x,$y);
        $this->SetFont('helvetica', 'I', $this->getFontSizePt()-4);

		// Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), $this->draw_borders, false, 'R', 0, '', 0, false, 'T', 'M');

		$this->footerR = new PDFRectangle($x, $y, $w, $this->getY() - $y); // Record dimensions of header for use by other elements
		$this->SetDefaultFont();
	}

	public function AddColor($po, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Default Color:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $po->COLOR, $this->draw_borders, 1);
		$this->colorR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}
	
	public function AddDocumentDate($date, $x, $y, $w, $h)
	{
		$this->AddDocumentLabelAndDate("Date",$date, $x, $y, $w, $h);
	}

	public function AddDocumentLabelAndDate($label, $date, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, $label.":", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, dol_print_date(strtotime($date),"%m/%d/%Y"), $this->draw_borders, 1);

		$this->documentDateR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}

	public function AddOfferings($po, $x, $y, $w, $h)
	{
		$this->SetXY($x, $y);
		if ($po->Check10YearsWarranty)
		{
			if ($this->NeedsNewPage($this->getFontSizePt()))
			{
				$this->AddPage('','',true);
				$this->SetXY($x, $this->headerR->GetBottom());
			}
			
			$this->writeWithCheckbox("Now Offering: ".$po->YearsWarranty." Years Warranty", $x, $this->GetY(), $w, $h);
		}
		if ($po->LifeTimeWarranty)
		{
			if ($this->NeedsNewPage($this->getFontSizePt()))
			{
				$this->AddPage('','',true);
				$this->SetXY($x, $this->headerR->GetBottom());
			}
			
			$this->writeWithCheckbox("Now Offering: Lifetime Warranty", $x, $this->GetY(), $w, $h);
		}
		if ($po->Check10YearsFreeMaintenance)
		{
			if ($this->NeedsNewPage($this->getFontSizePt()))
			{
				$this->AddPage('','',true);
				$this->SetXY($x, $this->headerR->GetBottom());
			}
			
			$this->writeWithCheckbox("Now Offering: 10 Years Free Maintenance Plan", $x, $this->GetY(), $w, $h);
		}
		if ($po->CheckNoPayment)
		{
			if ($this->NeedsNewPage($this->getFontSizePt()))
			{
				$this->AddPage('','',true);
				$this->SetXY($x, $this->headerR->GetBottom());
			}
			
			$this->writeWithCheckbox("Now Offering: No  Payment until job is completed", $x, $this->GetY(), $w, $h);
		}
		if ($po->CheckFreeOpeningClosing)
		{
			if ($this->NeedsNewPage($this->getFontSizePt()))
			{
				$this->AddPage('','',true);
				$this->SetXY($x, $this->headerR->GetBottom());
			}
			
			$this->writeWithCheckbox("Now Offering: Free Opening and Closing upon Customer request", $x, $this->GetY(), $w, $h);
		}

		$this->SetDefaultFont();
		$this->offeringsR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
		$this->SetX($x);
	}
	
	public function writeWithCheckbox($txt, $x, $y, $w, $h)
	{
		$this->SetFont('zapfdingbats', 'B', pdf_getPDFFontSize($this->outputlangs));
		$this->writeHTMLCell($this->getFontSizePt(), $this->getFontSizePt(), $x, $y, "o", 0, 0);
		$this->writeHTMLCell($this->getFontSizePt(), $this->getFontSizePt(), $x+1, $y-1, "3", 0, 0);
		$this->SetFont(pdf_getPDFFont($this->outputlangs),'B',pdf_getPDFFontSize($this->outputlangs));
		$this->writeHTMLCell($w-$this->getFontSizePt()*2, $h, $x+$this->getFontSizePt()/2, $y, $txt, 0, 1);
	}

	/**
	 *    Return state translated from an id. Return value is always utf8 encoded and without entities.
	 *
	 *    @param	int			$id         	id of state (province/departement)
	 *    @return   string      				String with state name (Return value is always utf8 encoded and without entities)
	 */
	function GetState($id,$withcode='')
	{
		$segments = explode( ':', $id );
		$id=$segments[0];
		$code=$segments[1];
		$label=$segments[2];

		if ($withcode == '1') 
			return $label;
		else if ($withcode == '2') 
			return $code;
		
		return $label;
	}

}
