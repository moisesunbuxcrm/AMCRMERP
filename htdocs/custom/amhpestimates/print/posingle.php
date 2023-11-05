<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';

$langs->load('amhpestimates@amhpestimates');

$id = GETPOST('id','alpha');
$templateName = GETPOST('tn','alpha');

$templateFile=dol_buildpath($reldir."custom/amhpestimates/core/modules/doc/pdf_".$templateName.".modules.php");
require_once $templateFile;

$poDB = new EaProductionOrders($db);
$poDB->fetch($id); 
$poDB->fetchItems();

$templateClass = "pdf_".$templateName;
$module = new $templateClass($db,$poDB);

if ($module->write_file($langs) == 0)
{
	setEventMessages($module->error, $module->errors, 'errors');
	dol_syslog($module->error, LOG_ERR);
}
