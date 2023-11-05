<?php
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhppermits/class/buildingpermit.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhppermits/include/parse.inc.php';
require_once TCPDF_PATH.'tcpdf.php';
require_once 'pdf_base.php';

/**
 *	Class to generate a contract PDF for a Production Order
 */
class pdf_permit extends pdf_base
{
	var $permit;
	var $template;

	function __construct($db, $permit, $template)
	{
		parent::__construct($db);
		global $conf,$langs;

		$this->permit = $permit;
		$this->template = $template;

		$this->description = $langs->trans('DocModelAzurDescription');
		$this->name = $this->template->name." ".$this->permit->ref." for ".$this->permit->buildingdeptcity;
	}

	function getPDFImpl($outputlangs)
	{
		return new pdf_permit_impl($this->db, $this->permit, $this->template, $this->name, $outputlangs);
	}

	function setFilename()
	{
		global $conf;

		// Definition of $dir and $file
		$basename = basename($this->template->filename);
		$permitid = dol_sanitizeFileName($this->permit->id);
		$permitfilename = dol_sanitizeFileName($basename." ".$this->permit->ref." for ".$this->permit->buildingdeptcity);
		$this->dir = $conf->amhppermits->dir_output . "/permit-" . $permitid;
		$this->filename =  $permitfilename . ".pdf";
		$this->filepath = $this->dir . "/" . $this->filename;
	}
}

class pdf_permit_impl extends pdf_base_impl 
{
	var $permit;
	var $template;

	function __construct($db, $permit, $template, $permitname, $outputlangs)
	{
		parent::__construct($db, $outputlangs,'P','in',[$template->pagewidth, $template->pageheight]);
		global $user,$conf;

		$this->permit = $permit;
		$this->template = $template;

		$this->SetTitle($this->outputlangs->convToOutputCharset($permitname));
		$this->SetFontSize($this->template->fontsize);
	}

	public function Header() {
		global $user,$conf;
		require_once DOL_DOCUMENT_ROOT . '/custom/amhppermits/include/utils.inc.php';

		$image_file = DOL_DOCUMENT_ROOT . '/custom/amhppermits/printing/'.$this->template->filename."_p".$this->getPage().".png";
		if (file_exists($image_file)) {
			$this->Image($image_file, 0, 0, $this->template->pagewidth, $this->template->pageheight, '', '', '', true);
		}
	}

	function generate()
	{
		for ($page=1; $page <= $this->template->pagecount; $page++) { 
			$this->AddPage('','',true); // Adds first page with header and footer automatically
			
			$template=new stdClass();
			$sql = 'SELECT rowid, fieldname, x, y, w, h ';
			$sql .= ' FROM `llx_ea_permittemplates_fields`';
			$sql .= ' WHERE ';
			$sql .= ' 	templateid = '.$this->template->rowid;
			$sql .= ' 	and pageno = '.$page;

			$result=$this->db->query($sql);
			if ($result)
			{
				$rowcount = $this->db->num_rows($result);
				if ($rowcount)
				{
					$fieldInfo = new stdClass();
					$resolver = new ExpressionResolver();
					for ($row=0; $row < $rowcount; $row++) { 
						$obj = $this->db->fetch_object($result);
						$fieldInfo->rowid = $obj->rowid;
						$fieldInfo->fieldname = $obj->fieldname;
						$fieldInfo->x = $obj->x;
						$fieldInfo->y = $obj->y;
						$fieldInfo->w = $obj->w;
						$fieldInfo->h = $obj->h;
						
						$resolver->resolve($this, $fieldInfo, $obj->fieldname, $this->permit, $isResolved);
					}
				}
			
				$this->db->free($result);
			
			}
			else
			{
				dol_print_error($this->db);
			}
		}
		
		$this->Close();
	}
}
