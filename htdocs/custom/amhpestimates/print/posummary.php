<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';

//$langs->load("admin");
//$langs->load("errors");
//$langs->load('other');
$langs->load('amhpestimates@amhpestimates');

$ids = GETPOST('ids','array');
$templateName = GETPOST('tn','alpha');

$templateFile=dol_buildpath($reldir."custom/amhpestimates/core/modules/doc/pdf_".$templateName.".modules.php");
require_once $templateFile;

$poDB = new EaProductionOrders($db);
$idString = implode(',', $ids);
$colors = $poDB->fetchColorsById($idString); 
$ponumbers = $poDB->fetchPONumbersById($idString); 

$templateClass = "pdf_".$templateName;
$module = new $templateClass($db,$ponumbers,$colors,$ids);

if ($module->write_file($langs) == 0)
{
	setEventMessages($module->error, $module->errors, 'errors');
	dol_syslog($module->error, LOG_ERR);
}
