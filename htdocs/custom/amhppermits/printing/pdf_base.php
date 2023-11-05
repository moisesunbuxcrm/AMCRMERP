<?php
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once TCPDF_PATH.'tcpdf.php';

/**
 *	Class to generate the basic elements of all AMHP PDF documents for permits
 */
class pdf_base
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
		$langs->load("amhppermits");

		$this->db = $db;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->version = "dolibarr";
		$this->name = "pdf_base";
		$this->type = "pdf";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_base_impl($this->db, $outputlangs);
	}

	function setFilename()
	{
		global $conf;

		// Definition of $dir and $file
		$permitid = dol_sanitizeFileName($this->permit->rowid);
		$permitname = dol_sanitizeFileName($this->template->name);
		$this->dir = $conf->amhppermits->dir_output . "/pdfs";
		$this->filename =  "default.pdf";
		$this->filepath = $this->dir . "/" . $this->filename;
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
		$outputlangs->load("amhppermits");

		$dest = GETPOST('dest','alpha'); // I (inline), D (download)
		if (!$dest)
			$dest='D';

		if ($conf->amhppermits->dir_output)
		{
			$this->setFilename();

			$pdf = $this->getPDFImpl($outputlangs);
			$pdf->generate();
			$pdf->Output($this->filename,$dest);

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
	var $displayNameOnly = false; // Change using query string - when true we display field names in PDF instead of values
	var $permitTextColor;
	var $permitErrorColor;

	function __construct($db, $outputlangs, $orientation='P', $unit, $dims)
	{
		parent::__construct($orientation, $unit, $dims);
		global $user,$conf;

		$this->db = $db;
		$this->outputlangs = $outputlangs;

		$this->Open();
		$this->setAutoPageBreak(false);

		$this->SetCreator("Dolibarr ".DOL_VERSION);
		$this->SetAuthor($this->outputlangs->convToOutputCharset($user->getFullName($this->outputlangs)));
		if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $this->SetCompression(false);

		$names = GETPOST('names','alpha');
		if ($names)
			$this->displayNameOnly = GETPOST('names','alpha') == "true"; // When true we display field names in PDF instead of values

		$this->permitErrorColor = array(255,0,0);
		$this->permitTextColor = array(0,0,0);
		$this->SetTextColor($this->permitTextColor[0],$this->permitTextColor[1],$this->permitTextColor[2]);
	}

	public function Header() {
	}

	public function Footer() {
	}

	public function writeFieldInfoValue($fieldInfo, $value, $border=0, $wrap=false, $alignV="top")
	{
		if ($this->displayNameOnly)
			$this->writeErrorText($fieldInfo->rowid.":".$fieldInfo->fieldname, $fieldInfo->x, $fieldInfo->y, $fieldInfo->w, $fieldInfo->h, $border, $wrap);
		else
			$this->writeText($value, $fieldInfo->x, $fieldInfo->y, $fieldInfo->w, $fieldInfo->h, $border, $wrap);
	}

	public function writeMoney($amt, $x, $y, $w, $h, $currency='$', $border=0)
	{
		if (!$amt)
			return;

		$this->SetXY($x, $y);
		$this->Cell($w, $h, $currency.number_format($amt, 2), $border);
	}

	public function writeText($txt, $x, $y, $w, $h, $border=0, $wrap=false, $alignV="B")
	{
		if (!$txt)
			return;

		$this->SetXY($x, $y);
		if ($wrap)
			$this->MultiCell($w, $h, $txt, $border, 'L', $false, 1, $x, $y-$this->getStringHeight($w, $txt), true, 0, false, true, 0, $alignV);
		else
			$this->Cell($w, $h, $txt, $border);
	}

	public function writeErrorText($txt, $x, $y, $w, $h, $border=0, $wrap=false, $alignV="B")
	{

		$this->SetXY($x, $y);
		$this->SetTextColor($this->permitErrorColor[0],$this->permitErrorColor[1],$this->permitErrorColor[2]);
		if ($wrap)
			$this->MultiCell($w, $h, $txt, $border, 'L', $false, 1, $x, $y-$this->getStringHeight($w, $txt), true, 0, false, true, 0, $alignV);
		else
			$this->Cell($w, $h, $txt, $border);
		$this->SetTextColor($this->permitTextColor[0],$this->permitTextColor[1],$this->permitTextColor[2]);
	}

	public function writeCheckmark($x, $y, $w, $h)
	{
		$ffamily = $this->GetFontFamily();
		$fstyle = $this->GetFontStyle();
		$this->SetFont('zapfdingbats');
		//$this->writeHTMLCell($this->getFontSizePt(), $this->getFontSizePt(), $x, $y, "o", 0, 0);
		$this->writeHTMLCell($this->getFontSizePt(), $this->getFontSizePt(), $x, $y-0.05, "3", 0, 0);
		$this->SetFont($ffamily,$fstyle);
	}

	public function writeDate($val, $x, $y, $w, $h, $border=0)
	{
		if (!$val)
			return;

		$this->SetXY($x, $y);
		$this->Cell($w, $h, dol_print_date($val,"%m/%d/%Y"), $border);
	}

	public function writeOval($x, $y, $w, $h)
	{
		// Ellipse( $x0, $y0, $rx, $ry = '', $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = array(), $fill_color = array(), $nc = 2 )
		$style = array('width' => 0.03, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->TextColor);
		$this->SetLineStyle($style);
		$rx=$w/2;
		$ry=$h/2;
		$this->Ellipse($x+$rx, $y+$ry,$rx,$ry);
	}

}
