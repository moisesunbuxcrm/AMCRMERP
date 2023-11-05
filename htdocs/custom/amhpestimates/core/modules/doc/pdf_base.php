<?php
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/core/modules/modules_po.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once TCPDF_PATH.'tcpdf.php';

/**
 *	Class to generate the basic elements of all AMHP PDF documents for estimates and PO's
 */
class pdf_base extends PODocModel
{
	var $db;
	var $name;
	var $version;
	var $type;
	var $page_width;
	var $page_height;
	var $option_logo = 1;

	var $dir;
	var $filename;
	var $filepath;

	function __construct($db)
	{
		global $conf,$langs;

		$langs->load("main");
		$langs->load("amhpestimates");

		$this->db = $db;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->version = "dolibarr";
		$this->name = "pdf_base";
		$this->type = "pdf";

		$formatarray=pdf_getFormat();
		$this->page_width = $formatarray['width'];
		$this->page_height = $formatarray['height'];
	}

	/**
     *  Function to build pdf onto disk
     *
     *  @param		Translate	$outputlangs		Lang output object
	 */
	function write_file($outputlangs)
	{
		global $langs, $conf;
		
		if (! is_object($outputlangs)) $outputlangs=$langs;

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("amhpestimates");

		if ($conf->amhpestimates->dir_output)
		{
			$this->setFilename();

			$pdf = $this->getPDFImpl($outputlangs);
			$pdf->generate();
			$pdf->Output($this->filename,'D');

			return 1;   // Pas d'erreur
		}
		else
		{
			$this->error=$outputlangs->trans("ErrorConstantNotDefined","PROP_OUTPUTDIR");
			return 0;
		}
	}

}

class pdf_base_impl extends TCPDF 
{
	var $db;
	var $outputlangs;
	var $draw_borders = 0;

	var $headerR, $footerR;
	var $pageR, $observationR;
	var $customerInfoR, $customerInfoPhonesR, $contactInfoR, $contactInfoPhonesR;
	var $itemsHeaderR, $lastItemR, $vendorInfoR;

	function __construct($db, $outputlangs, $orientation='P')
	{
		$fmt =  pdf_getFormat();
		$unit = $fmt["unit"];
		$dims = [$fmt["width"], $fmt["height"]];
		parent::__construct($orientation, $unit, $dims);
		global $user,$conf;

		$this->db = $db;
		$this->outputlangs = $outputlangs;

		$this->SetDefaultFont();
		$this->SetDefaultDrawingColor();
		$this->Open();
		$this->setAutoPageBreak(false);

		$this->SetCreator("Dolibarr ".DOL_VERSION);
		$this->SetAuthor($this->outputlangs->convToOutputCharset($user->getFullName($this->outputlangs)));
		if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $this->SetCompression(false);

		$left_margin=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$right_margin=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$top_margin =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$bottom_margin =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;
		$this->SetMargins($left_margin, $top_margin, $right_margin);

		$this->pageR = new PDFRectangle(
			$left_margin, $top_margin, 
			$this->getPageWidth()-$left_margin-$right_margin, 
			$this->getPageHeight()-$top_margin-$bottom_margin);
	}

	public function AddObservation($txt, $x, $y, $w, $h, $border=0)
	{
		$this->SetXY($x, $y);
		if (!$txt)
		{
			$this->observationR = new PDFRectangle($x, $y, $w, 0); // Record dimensions for use by other elements
			return;
		}

		if ($this->NeedsNewPage($gap + $this->getFontSize()*3))
		{
			$this->AddPage('','',true);
			$y = $this->headerR->GetBottom();
			$this->SetXY($x, $y);
		}

		$this->Ln($gap);
		$y = $this->GetY();
		$this->SetX($x);

		$this->writeHTMLCell($w, 0, $x, $y, nl2br($txt), $border, 1, false, true, 'L');

		$this->observationR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->GetY()-$y); // Record dimensions for use by other elements
	}

	public function AddCustomerInfo($po, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Customer Name:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CUSTOMERNAME), $this->draw_borders, 'L', false, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Customer Address:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CUSTOMERADDRESS), $this->draw_borders, 'L', false, 1);

		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CITY).", ".strtoupper($po->STATE)." ".strtoupper($po->ZIPCODE), $this->draw_borders, 'L', false, 1);

		$this->customerInfoR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}
	
	public function AddCustomerInfoPhones($po, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Cell Phone:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->format_telephone($po->PHONENUMBER1), $this->draw_borders, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Secondary Phone:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->format_telephone($po->PHONENUMBER2), $this->draw_borders, 1);

		/*$this->SetX($x);
		//$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		//$this->Cell($column_width, $h, "Customer Fax:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->format_telephone($po->FAXNUMBER), $this->draw_borders, 1);*/

		$this->customerInfoPhonesR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}

	public function AddContactInfo($po, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Contact Name:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CONTACTNAME), $this->draw_borders, 'L', false, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Contact Address:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CUSTOMERADDRESS), $this->draw_borders, 'L', false, 1);

		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h, strtoupper($po->CITY).", ".strtoupper($po->STATE)." ".strtoupper($po->ZIPCODE), $this->draw_borders, 'L', false, 1);

		$this->contactInfoR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}
	
	public function AddContactInfoPhones($po, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Contact Phone:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->format_telephone($po->CONTACTPHONE1), $this->draw_borders, 1);

		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Contact Mobile:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->Cell($column_width, $h, $this->format_telephone($po->CONTACTPHONE2), $this->draw_borders, 1);

		$this->contactInfoPhonesR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}

	public function AddVendorInfo($x, $y, $w, $h)
	{
		
		$this->SetXY($x,$y-4);

		$midpoint_margin = 2;
		$column_width = $w/2-$midpoint_margin/2;
		
		$sql2=  "SELECT barcode AS barcode FROM  ".MAIN_DB_PREFIX."societe ";
        $sql2.= " WHERE  rowid = " . $this->po->customerId;
        $resql2 = $this->db->query($sql2);
        foreach($resql2 as $value){

            $barcode= $value["barcode"] ;

        }
		
		
		$sql3=  "SELECT u.email AS ma, u.user_mobile  AS ph FROM  ".MAIN_DB_PREFIX."ea_po as p ";
        $sql3.= " LEFT JOIN llx_user AS u ON p.Salesman = concat(u.firstname,' ',u.lastname)";
        $sql3.= " WHERE p.PONUMBER = '" . $this->po->PONUMBER . "'";
        $resql3 = $this->db->query($sql3);
        foreach($resql3 as $value){

            $ma= $value["ma"] ;
            $ph= $value["ph"] ;

        }
		
		$phone = '<b>Phone: </b>';
		$mail = '<b>Mail </b>';

		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h, "Sales Rep.:", $this->draw_borders, 0, "R");
		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h+13, "Phone:", $this->draw_borders, 0, "R");
		$this->SetX($x);
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		$this->Cell($column_width, $h+21, "email:", $this->draw_borders, 0, "R");
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt());
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h-10, strtoupper($this->po->Salesman), $this->draw_borders, 'L', false, 1);
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width,$h-10 , $ph, $this->draw_borders, 'L', false, 1);
		$this->SetX($x+$column_width+$midpoint_margin);
		$this->MultiCell($column_width, $h-10 , $ma, $this->draw_borders, 'L', false, 1);
     
		 
		$this->vendorInfoR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		$this->SetDefaultFont();
	}
	
	public function AddItemsArea($po, $itemHeaders, $x, $y, $w, $h, $filterByType=null)
	{
		// Check full width which may be more than 100%
		$fullWidth = 0;
		foreach($itemHeaders as $hdr)
		{
			$fullWidth += $hdr['width']*$w;
		}

		if ($w != $fullWidth)
			$x -= ($fullWidth-$w)/2; // Adjust $x to center table

		$this->SetXY($x,$y+10);
		
		$needsItemsHeader = true;
		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
		foreach($po->items as $item)
		{
			if ($filterByType==null || $item->PRODUCTTYPENAME == $filterByType)
			{
				if ($this->NeedsNewPage($this->getFontSizePt()*1.5))
				{
					$this->AddPage('','',true);
					$this->SetXY($x,$this->headerR->GetBottom());
					$needsItemsHeader = true;
				}

				if ($needsItemsHeader)
				{
					$this->AddItemsHeader($itemHeaders, $x, $this->GetY(), $w, 0);
					$needsItemsHeader = false;
				}

				$this->AddItem($itemHeaders, $item, $x, $this->GetY(), $w, $h);
			}
		}
	}
	
	public function AddItem($itemHeaders, $item, $x, $y, $w, $h)
	{
		$this->SetXY($x,$y);

		$margins = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);

		// Determine row height for this row by checking all values in row
		$maxHeight = $this->getStringHeight($w, " ", false, true, $margins, 1);
		foreach($itemHeaders as $hdr)
		{
			$val = $item->{$hdr['prop']};
			if ($hdr['format'])
				$val = $hdr['format']($val);
			if ($val === "")
				$val = " ";
			$thisHeight = $this->getStringHeight($hdr['width']*$w, $val, false, true, $margins, 1);
			if ($maxHeight < $thisHeight)
				$maxHeight = $thisHeight;
		}

		foreach($itemHeaders as $hdr)
		{
			$val = $item->{$hdr['prop']};
			if ($hdr['format'])
				$val = $hdr['format']($val);
			if ($val === "")
				$val = " ";
			$this->MultiCell($hdr['width']*$w, $maxHeight, $val, 1, 'C', false, 0, '', '', true, 0, false, true, $maxHeight, 'M');
		}
		$this->Ln($maxHeight);

		$this->lastItemR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements
	}
	
	public function GetCEO() { global $conf; return strtoupper($conf->global->MAIN_INFO_SOCIETE_MANAGERS); }
	public function GetOrganizationName() { global $conf; return strtoupper($conf->global->MAIN_INFO_SOCIETE_NOM); }
	public function GetOrganizationNote() { global $conf; return $conf->global->MAIN_INFO_SOCIETE_NOTE; }
	public function GetOrganizationPhone() { global $conf; return $conf->global->MAIN_INFO_SOCIETE_TEL; }
	public function GetOrganizationFax() { global $conf; return $conf->global->MAIN_INFO_SOCIETE_FAX; }
	public function GetOrganizationAddress() { 
		global $conf; 
		return 
			$conf->global->MAIN_INFO_SOCIETE_ADDRESS.", ".
			$conf->global->MAIN_INFO_SOCIETE_TOWN.", ".
			$this->GetState($conf->global->MAIN_INFO_SOCIETE_STATE, 2).", ".
			$conf->global->MAIN_INFO_SOCIETE_ZIP; 
	}

	public function SetDefaultFont() { $this->SetFont(pdf_getPDFFont($this->outputlangs),'',pdf_getPDFFontSize($this->outputlangs)); }
	public function SetDefaultDrawingColor() { $this->SetDrawColor(128,128,128); }

	public function NeedsNewPage($h)
	{
		//dol_syslog("NeedsNewPage():Condition = ".($this->GetY() + $h)." > ".$this->pageR->GetBottom()."? (Position= ".$this->GetX().", ".$this->GetY().")");
		return $this->GetY() + $h > $this->pageR->GetBottom();
	}

	function format_telephone($phone_number)
	{
		if ($phone_number)
		{
			$cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
			preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
			return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
		}
		return "";
	}
}

class PDFRectangle {
	var $x;
	var $y;
	var $w;
	var $h;

	function __construct($x=0, $y=0, $w=0, $h=0) {
		$this->x = $x;
		$this->y = $y;
		$this->w = $w;
		$this->h = $h;
	}

	function GetLeft() { return $this->x; }
	function GetRight() { return $this->x+$this->w; }
	function GetTop() { return $this->y; }
	function GetBottom() { return $this->y+$this->h; }
}