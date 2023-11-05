<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item_design.class.php';

top_httphead();
header("Content-Type: text/json");

$id = GETPOST("id", 'int');

if (!$id)
{
  echo '{ "msg": "delete(): Missing id parameter" }';
}
else
{
  $eii = new EaEstimateItemDesign($db);
  if ($eii->fetch($id))
  {
    if ($eii->delete(null) == 1) {
      $ei = new EaEstimateItem($db);
      if ($ei->fetch($eii->estimateitemid))
      {
        if ($ei->delete(null) == 1)
          echo '{ "msg": "OK - deleted item '.$ei->id.' and design product." }';
        else
          echo '{ "error": "Delete failed: ' . $ei->error . '" }';
      }
      else
      {
        if ($ei->error)
          echo '{ "error": "Delete failed: ' . $ei->error . '" }';
        else
          echo '{ "error": "Delete failed: Unknown id=' . $id . '" )';
      }
    }
    else
      echo '{ "error": "Delete failed: ' . $eii->error . '" }';
  }
  else
  {
    if ($eii->error)
      echo '{ "msg": "Delete failed: ' . $eii->error . '" }';
    else
      echo '{ "msg": "Delete failed: Unknown id=' . $id . '" )';
  }
}
