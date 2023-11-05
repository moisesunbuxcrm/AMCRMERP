<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhppermits/class/buildingpermit.class.php';
require_once 'pdf_permit.php';

$langs->load('amhppermits@amhppermits');

$tid = GETPOST('tid','int');
$pid = GETPOST('pid','int');

$permit = new BuildingPermit($db);
$permit->fetch($pid);

// Load template info from database
$template=new stdClass();
$sql = 'SELECT t.rowid, t.name, t.filename, t.fontsize, t.pagewidth, t.pageheight, t.pagecount';
$sql .= ' FROM `llx_ea_permittemplates` t ';
$sql .= ' left join llx_ea_permittemplates_builddepts pb on pb.template_id = t.rowid';
$sql .= ' left join llx_ea_builddepts b on b.rowid = pb.builddept_id';
$sql .= ' WHERE b.city_name = \''.$permit->buildingdeptcity.'\'';
$sql .= ' and t.rowid = '.$tid;
$result=$db->query($sql);
if ($result)
{
	if ($db->num_rows($result))
	{
		$obj = $db->fetch_object($result);
		$template->rowid = $obj->rowid;
		$template->name = $obj->name;
		$template->filename = $obj->filename;
		$template->fontsize = $obj->fontsize;
		$template->pagewidth = $obj->pagewidth;
		$template->pageheight = $obj->pageheight;
		$template->pagecount = $obj->pagecount;
	}

	$db->free($result);

}
else
{
	dol_print_error($db);
}
$pdf = new pdf_permit($db,$permit,$template);

if ($pdf->write_file($langs) == 0)
{
	setEventMessages($pdf->error, $pdf->errors, 'errors');
	dol_syslog($pdf->error, LOG_ERR);
}
