<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require '../include/utils.inc.php';

top_httphead();
header("Content-Type: text/json");

$copyPOID = GETPOST("POID", 'int');
$initials = getInitialsFor($user);

if (!$copyPOID)
{
  echo '{ "msg": "update(): Missing or empty POID" }';
}
else
{
  $poDB = new EaProductionOrders($db);
  $newPOID = $poDB->duplicate($copyPOID, $initials);
  if ($newPOID > 0)
  {
    echo '{ "msg": "OK", "newPOID": ' . $newPOID . ' }';
  }
  else
  {
    echo '{ "msg": "Create failed: ' . $poDB->error . '"}';
  }
}