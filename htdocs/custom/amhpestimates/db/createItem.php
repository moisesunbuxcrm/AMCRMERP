<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';

top_httphead();
header("Content-Type: text/json");

$itemDB = new EaProductionOrderItems($db);

$itemDB->POID = GETPOST("POID", 'int');
$itemDB->LineNumber = GETPOST("LineNumber", 'int');
$itemDB->OPENINGW = GETPOST("OPENINGW", 'int');
$itemDB->OPENINGHT = GETPOST("OPENINGHT", 'int');
$itemDB->TRACK = GETPOST("TRACK", 'int');
$itemDB->TYPE = GETPOST("TYPE", 'alpha');
$itemDB->BLADESQTY = GETPOST("BLADESQTY", 'int');
$itemDB->BLADESSTACK = GETPOST("BLADESSTACK", 'int');
$itemDB->BLADESLONG = GETPOST("BLADESLONG", 'int');
$itemDB->LEFT = GETPOST("LEFT", 'alpha');
$itemDB->RIGHT = GETPOST("RIGHT", 'alpha');
$itemDB->LOCKIN = GETPOST("LOCKIN", 'alpha');
$itemDB->LOCKSIZE = GETPOST("LOCKSIZE", 'alpha');
$itemDB->UPPERSIZE = GETPOST("UPPERSIZE", 'int');
$itemDB->UPPERTYPE = GETPOST("UPPERTYPE", 'alpha');
$itemDB->LOWERSIZE = GETPOST("LOWERSIZE", 'int');
$itemDB->LOWERTYPE = GETPOST("LOWERTYPE", 'alpha');
$itemDB->ANGULARTYPE = GETPOST("ANGULARTYPE", 'alpha');
$itemDB->ANGULARSIZE = GETPOST("ANGULARSIZE", 'int');
$itemDB->ANGULARQTY = GETPOST("ANGULARQTY", 'int');
$itemDB->MOUNT = GETPOST("MOUNT", 'alpha');
$itemDB->ALUMINST = GETPOST("ALUMINST", 'int');
$itemDB->LINEARFT = GETPOST("LINEARFT", 'int');
$itemDB->OPENINGHT4 = GETPOST("OPENINGHT4", 'int');
$itemDB->ALUMINST4 = GETPOST("ALUMINST4", 'int');
$itemDB->EST8HT = GETPOST("EST8HT", 'int');
$itemDB->ALUM = GETPOST("ALUM", 'int');
$itemDB->WINDOWSTYPE = GETPOST("WINDOWSTYPE", 'alpha');
$itemDB->EXTRAANGULARTYPE = GETPOST("EXTRAANGULARTYPE", 'alpha');
$itemDB->EXTRAANGULARSIZE = GETPOST("EXTRAANGULARSIZE", 'int');
$itemDB->EXTRAANGULARQTY = GETPOST("EXTRAANGULARQTY", 'int');
$itemDB->SQFEETPRICE = GETPOST("SQFEETPRICE", 'int');
$itemDB->PRODUCTTYPE = GETPOST("PRODUCTTYPE", 'alpha');
$itemDB->COLOR = GETPOST("COLOR", 'alpha');
$itemDB->MATERIAL = GETPOST("MATERIAL", 'alpha');
$itemDB->PROVIDER = GETPOST("PROVIDER", 'alpha');
$itemDB->INSTFEE = GETPOST("INSTFEE", 'int');
$itemDB->TUBETYPE = GETPOST("TUBETYPE", 'alpha');
$itemDB->TUBESIZE = GETPOST("TUBESIZE", 'int');
$itemDB->TUBEQTY = GETPOST("TUBEQTY", 'int');

if ($itemDB->create() == 1)
{
  echo '{ "msg": "OK", "PODescriptionID": ' . $itemDB->PODescriptionID . ' }';
}
else{
  echo '{ "msg": "Create failed: ' . $itemDB->error . '"}';
}