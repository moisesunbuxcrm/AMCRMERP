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
class pdf_posummary_base extends pdf_base
{
	function __construct($db)
	{
		parent::__construct($db);
		$this->name = "pdf_posummary_base";
	}
}

class pdf_posummary_base_impl extends pdf_base_impl 
{
	var $headerTitle;

	function __construct($db, $outputlangs)
	{
		parent::__construct($db, $outputlangs, 'L');
		global $user,$conf;

		$headerTitle = "Production Order Summary Report";
	}

	//Page header
    public function Header() {
		global $conf;

		$this->SetDefaultDrawingColor();
		$x = $this->pageR->x;
		$w = $this->pageR->w;
		$y = $this->pageR->y;

		$this->SetXY($x,$y);
		
		$fontSize = $this->getHeaderFontSize();
		$this->Ln($fontSize/2);

		$this->SetFont($this->getFontFamily(), 'B', $fontSize+2);
		$this->Cell($w, $fontSize, $this->GetOrganizationName(), $this->draw_borders, 1, 'R', 0, '', 0, false, 'M', 'M');

		$this->SetFont($this->getFontFamily(), 'B', $fontSize-2);
		$this->Cell($w, $fontSize, $this->headerTitle, $this->draw_borders, 1, 'R', 0, '', 0, false, 'M', 'M');

		$this->addAdditionalHeaderInfo();
		
		$this->headerR = new PDFRectangle($x, $y+$this->getFontSize(), $w, $this->getY() - $y); // Record dimensions of header for use by other elements

        $image_file = $conf->mycompany->dir_output."/logos/".$conf->global->MAIN_INFO_SOCIETE_LOGO;
		$this->Image($image_file, 10, $this->getImageTopMargin(), 0, $this->headerR->h, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetDefaultFont();
	}
	
	public function getHeaderFontSize()
	{
		return $this->getFontSizePt();
	}

	public function getImageTopMargin()
	{
		return 10;
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

	function addSummaryTable($color,$items,$x,$y,$w,$h,$filterByType=null)
	{
		$this->SetXY($x, $y);
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());
		if (!$color)
			$color="NONE";

		$margins = array('T' => 1, 'R' => 1, 'B' => 1, 'L' => 1);
		$cellHeight = $this->getStringHeight($w, " ", false, true, $margins, 1);

		if ($this->NeedsNewPage($cellHeight*4.5)) // Title + headers + single row
		{
			$this->AddPage('','',true);
			$this->SetXY($x,$this->headerR->GetBottom());
		}

		$this->Cell($w, $h, "SUMMARY OF AMOUNTS:".$color, $this->draw_borders, 1, "C");
		$this->Ln($this->getFontSizePt()/2);

		$cw = .0434; // 23 columns
		$headers = array(
			array("name"=>"Blades","width"=>$cw*2, // 23 columns
				"children"=>array(
					array("name"=>"Long","width"=>.5,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "SUMBLADESLONG", "BLADESLONG"),2); }),
					array("name"=>"Qty", "width"=>.5,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "SUMBLADESLONG", "SumOfBLADESQTY"),2); }),
				)
			),
			array("name"=>"","width"=>$cw*2,
				"children"=>array(
					array("name"=>"Starters","width"=>.6,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "STARTERS", "BLADESLONG"),2); }),
					array("name"=>"Qty",     "width"=>.4,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "STARTERS", "STARTERSQTY"); }),
				)
			),
			array("name"=>"","width"=>$cw*2,
				"children"=>array(
					array("name"=>"Center\nMale","width"=>.6,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "CENTERMALE", "BLADESLONG"),2); }),
					array("name"=>"Qty",     "width"=>.4,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "CENTERMALE", "CountOfBLADESLONG"); }),
				)
			),
			array("name"=>"","width"=>$cw*2,
				"children"=>array(
					array("name"=>"Center\nFemale","width"=>.6,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "CENTERFEMALE", "BLADESLONG"),2); }),
					array("name"=>"Qty",     "width"=>.4,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "CENTERFEMALE", "CountOfBLADESLONG"); }),
				)
			),
			array("name"=>"Upper Track","width"=>$cw*3,
				"children"=>array(
					array("name"=>"Type","width"=>.33,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "UPPERTRACK", "UPPERTYPE"); }),
					array("name"=>"Size","width"=>.33,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "UPPERTRACK", "UPPERSIZE"),2); }),
					array("name"=>"Qty", "width"=>.34,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "UPPERTRACK", "CountOfUPPERSIZE"); }),
				)
			),
			array("name"=>"Lower Track","width"=>$cw*3,
				"children"=>array(
					array("name"=>"Type","width"=>.33,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "LOWERTRACK", "LOWERTYPE"); }),
					array("name"=>"Size","width"=>.33,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "LOWERTRACK", "LOWERSIZE"),2); }),
					array("name"=>"Qty", "width"=>.34,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "LOWERTRACK", "CountOfLOWERSIZE"); }),
				)
			),
			array("name"=>"Angular","width"=>$cw*3,
				"children"=>array(
					array("name"=>"Type","width"=>.33,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "ANGULAR", "ANGULARTYPE"); }),
					array("name"=>"Size","width"=>.33,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "ANGULAR", "ANGULARSIZE"),2); }),
					array("name"=>"Qty", "width"=>.34,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "ANGULAR", "SumOfANGULARQTY"); }),
				)
			),
			array("name"=>"Extra Ang","width"=>$cw*3,
				"children"=>array(
					array("name"=>"Type","width"=>.33,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "EXTRAANG", "EXTRAANGULARTYPE"); }),
					array("name"=>"Size","width"=>.33,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "EXTRAANG", "EXTRAANGULARSIZE"),2); }),
					array("name"=>"Qty", "width"=>.34,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "EXTRAANG", "SumOfEXTRAANGULARQTY"),2); }),
				)
			),
			array("name"=>"Tubes","width"=>$cw*3,
				"children"=>array(
					array("name"=>"Type","width"=>.33,"getData"=>function($data, $rowIndex) { return $this->extractProperty($data, $rowIndex, "TUBES", "TUBETYPE"); }),
					array("name"=>"Size","width"=>.33,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "TUBES", "TUBESIZE"),2); }),
					array("name"=>"Qty", "width"=>.34,"getData"=>function($data, $rowIndex) { return round((float)$this->extractProperty($data, $rowIndex, "TUBES", "SumOfTUBEQTY"),2); }),
				)
			),
		);

		// Collect data
		$rows = array();
		$maxrows = 0;
		$itemArray = $items;
		if ($filterByType != null)
			$itemArray = array_filter($itemArray, function($e) {return $e->PRODUCTTYPENAME == $filterByType;});
		$idArray = array_map(function($e) {return $e->PODescriptionID;}, $items);
		$ids = implode(",",$idArray);
		$rows['SUMBLADESLONG'] = EaProductionOrderItems::posummary_fetchSUMBLADESLONG($this->db,$ids);
		$c = count($rows['SUMBLADESLONG']); if ($c > $maxrows) $maxrows = $c;
		$rows['STARTERS'] = EaProductionOrderItems::posummary_fetchSTARTERS($this->db,$ids);
		$c = count($rows['STARTERS']); if ($c > $maxrows) $maxrows = $c;
		$rows['CENTERMALE'] = EaProductionOrderItems::posummary_fetchCENTERMALE($this->db,$ids);
		$c = count($rows['CENTERMALE']); if ($c > $maxrows) $maxrows = $c;
		$rows['CENTERFEMALE'] = EaProductionOrderItems::posummary_fetchCENTERFEMALE($this->db,$ids);
		$c = count($rows['CENTERFEMALE']); if ($c > $maxrows) $maxrows = $c;
		$rows['UPPERTRACK'] = EaProductionOrderItems::posummary_fetchUPPERTRACK($this->db,$ids);
		$c = count($rows['UPPERTRACK']); if ($c > $maxrows) $maxrows = $c;
		$rows['LOWERTRACK'] = EaProductionOrderItems::posummary_fetchLOWERTRACK($this->db,$ids);
		$c = count($rows['LOWERTRACK']); if ($c > $maxrows) $maxrows = $c;
		$rows['ANGULAR'] = EaProductionOrderItems::posummary_fetchANGULAR($this->db,$ids);
		$c = count($rows['ANGULAR']); if ($c > $maxrows) $maxrows = $c;
		$rows['EXTRAANG'] = EaProductionOrderItems::posummary_fetchEXTRAANG($this->db,$ids);
		$c = count($rows['EXTRAANG']); if ($c > $maxrows) $maxrows = $c;
		$rows['TUBES'] = EaProductionOrderItems::posummary_fetchTUBES($this->db,$ids);
		$c = count($rows['TUBES']); if ($c > $maxrows) $maxrows = $c;
		
		$columnGap = 1;
		$colGroupCount = count($headers);
		$netWidth = $w-$columnGap*($colGroupCount-1);

		$this->addSummaryData($x, $headers, $netWidth, $columnGap, $rows, $maxrows);
	}

	function addSummaryHeaders($x, $headers, $netWidth, $columnGap)
	{
		$this->SetDefaultFont();
		$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt()-2);
		$rowHeight = ($this->getFontSizePt()+2)*2;

		// Print headers
		$left = $x;
		$top = $this->GetY();
		foreach($headers as $header)
		{
			$this->SetXY($left, $top);
			$headerWidth = $netWidth*$header['width'];
			$headerHeight = $rowHeight;
			$childTop = $top;
			if ($header['name'])
			{
				$headerHeight = $headerHeight/2;
				$this->MultiCell($headerWidth, $headerHeight/2, $header['name'], 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight/2, 'M');
				$childTop = $childTop + $headerHeight/2;
			}

			$childCount = count($header['children']);
			$childLeft = $left;
			foreach($header['children'] as $child)
			{
				$this->SetXY($childLeft, $childTop);
				$childWidth = $headerWidth * $child['width'];
				$this->MultiCell($childWidth, $headerHeight/2, $child['name'], 1, 'C', false, 0, '', '', true, 0, false, true, $headerHeight/2, 'M');
				$childLeft = $childLeft + $childWidth;
			}

			$left = $left + $headerWidth + $columnGap;
		}
		$this->Ln($headerHeight/2);
	}

	function addSummaryData($x, $headers, $netWidth, $columnGap, $rows, $maxrows)
	{
		$margins = array('T' => 1, 'R' => 1, 'B' => 1, 'L' => 1);
		$rowHeight = $this->getStringHeight($netWidth, " ", false, true, $margins, 1);
		$needHeaders = true;

		// Display data
		for($rowIndex=0; $rowIndex < $maxrows; $rowIndex++)
		{
			if ($this->NeedsNewPage($rowHeight*2))
			{
				$this->AddPage('','',true);
				$this->SetXY($x,$this->headerR->GetBottom());
				$needHeaders = true;
			}

			if ($needHeaders)
			{
				if ($this->NeedsNewPage($rowHeight*3))
				{
					$this->AddPage('','',true);
					$this->SetXY($x,$this->headerR->GetBottom());
				}

				$this->addSummaryHeaders($x, $headers, $netWidth, $columnGap);
				$needHeaders = false;

				$this->SetDefaultFont();
				$this->SetFont($this->getFontFamily(), '', $this->getFontSizePt()-2);
				$rowHeight = $this->getStringHeight($netWidth, " ", false, true, $margins, 1);
			}

			$left = $x;
			$top = $this->GetY();
			foreach($headers as $header)
			{
				$this->SetXY($left, $top);
				$headerWidth = $netWidth*$header['width'];
				$childTop = $top;
	
				$childCount = count($header['children']);
				$childLeft = $left;
				foreach($header['children'] as $child)
				{
					$this->SetXY($childLeft, $childTop);
					$childWidth = $headerWidth * $child['width'];
					$val = $child['getData']($rows, $rowIndex);
					if ($val == "")
						$val = " "; // Avoid larger cell when the text is empty.
					$this->MultiCell($childWidth, $rowHeight, $val, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

					$childLeft = $childLeft + $childWidth;
				}
	
				$left = $left + $headerWidth + $columnGap;
			}
			$this->Ln($rowHeight);
		}

		$this->summaryDataBottom = $this->GetY();
	}
}
