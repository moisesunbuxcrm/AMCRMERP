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

$id = $_GET['id'];

if (!$id)
{
  echo '{ "msg": "delete(): Missing id parameter" }';
}
else
{
  $poDB = new EaProductionOrderItems($db);
  if (!$poDB->error)
  {
    if ($poDB->deleteByPOID($id) == 1)
      echo '{ "msg": "OK" }';
    else
      echo '{ "msg": "Delete failed: ' . $poDB->error . '" }';
  }
  else
  {
    if ($poDB->error)
      echo '{ "msg": "Delete failed: ' . $poDB->error . '" }';
    else
      echo '{ "msg": "Delete failed: Unknown POID=' . $id . '" )';
  }
}
