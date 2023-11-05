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
 *	Class to generate a standard PDF Production Order Summary report
 */
class pdf_posummary_standard extends pdf_posummary_base
{
	var $posIds;
	var $colors;

	function __construct($db, $ponumbers, $colors, $posIds)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->ponumbers = $ponumbers;
		$this->colors = $colors;
		$this->posIds = $posIds;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = "posummary_standard";
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_posummary_standard_impl($this->db, $this->ponumbers, $this->colors, $this->posIds, $outputlangs);
	}

	function setFilename()
	{
		global $conf;

		// Definition of $dir and $file
		$ref = date("Y-m-d-H-i-s");
		$this->dir = $conf->amhpestimates->dir_output . "/posummary-" . $ref;
		$this->filename = "posummary-" . $ref . ".pdf";
		$this->filepath = $this->dir . "/" . $this->filename;
	}
}

class pdf_posummary_standard_impl extends pdf_posummary_base_impl 
{
	var $ponumbers, $colors, $posIds;
	var $poNumbersR;

	var $summaryDataBottom;

	function __construct($db, $ponumbers, $colors, $ids, $outputlangs)
	{
		parent::__construct($db, $outputlangs);
		global $user,$conf;

		$this->ponumbers = $ponumbers;
		$this->colors = $colors;
		$this->posIds = $ids;

		$this->SetTitle($this->outputlangs->convToOutputCharset("Production Order Summary"));
		$this->SetSubject($this->outputlangs->transnoentities("PdfCommercialProposalTitle"));
	}

	function generate()
	{
		$this->AddPage('','',true); // Adds first page with header and footer automatically
		$this->SetXY($this->pageR->x, $this->headerR->GetBottom());

		$this->addProductionOrderNumbers($this->ponumbers, $this->pageR->x, $this->headerR->GetBottom(), $this->pageR->w, 0);
		
		$top = $this->poNumbersR->GetBottom()+$this->getFontSizePt();
		foreach($this->colors as $color)
		{
			$items = EaProductionOrderItems::fetchByColorsAndPOID($this->db, $color, implode(',', $this->posIds));
			$this->addSummaryTable($color, $items, $this->pageR->x, $top, $this->pageR->w, 0);
			$top = $this->summaryDataBottom+$this->getFontSizePt();
		}

		$this->Close();
	}

	public function addAdditionalHeaderInfo()
	{
		$fontSize = $this->getFontSizePt();
		$this->SetFont($this->getFontFamily(), 'B', $fontSize+2);
		$this->Cell($this->pageR->w, $fontSize, "Accordions", $this->draw_borders, 1, 'R', 0, '', 0, false, 'M', 'M');
	}

	function addProductionOrderNumbers($ponumbers, $x, $y, $w, $h)
	{
		$label =  "Production Order Numbers:";
		$this->SetFont($this->getFontFamily(), 'B', $this->getFontSizePt());

		$cell_margin = 3;
		$label_width = $this->GetStringWidth($label);
		$left = $x + $label_width + $cell_margin;
		$cell_width = ($w-$left)/3;
		$column_width = $cell_width-$cell_margin*2;
		$needLabel = true;

		$this->SetXY($x,$y);
		//for ($i = 0; $i < 3; $i++)
			foreach($ponumbers as $ponumber)
			{
				if ($this->NeedsNewPage($this->getFontSizePt()))
				{
					// Draw boxes around exsting numbers and start new page.
					$this->draw2Boxes($left,$y,$cell_width,$this->getY()-$y);

					$this->AddPage('','',true);
					$this->SetXY($x,$this->headerR->GetBottom());
					$needLabel = true;
				}

				if ($needLabel)
				{
					$this->SetTextColor(0,0,0);
					$this->Cell($column_width, $h, $label, $this->draw_borders, 0, "L");
					$needLabel = false;
				}

				$this->SetTextColor(235,46,65);
				$this->SetX($left+$cell_margin);
				$this->Cell($column_width, $h, $ponumber, $this->draw_borders, 1, "L");
			}

		$this->poNumbersR = new PDFRectangle($x, $y, $w, $h>0?$h:$this->getY()-$y); // Record dimensions for use by other elements

		$this->SetColor(0,0,0);
		//$this->draw2Boxes($left,$y,$cell_width,$this->getY()-$y);
		$h = $this->getY()-$y;
		$this->SetXY($left,$y);
		$this->Cell($cell_width, $h, "", 1);
		$this->SetTextColor(0,0,0);
	}

	function draw2Boxes($x,$y,$w,$h)
	{
		$this->SetXY($x,$y);
		$this->Cell($w, $h, "", 1);
		$this->SetXY($x+$w,$y);
		$this->Cell($w, $h, "", 1);
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
