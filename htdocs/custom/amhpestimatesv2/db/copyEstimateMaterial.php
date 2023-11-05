<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item_material.class.php';
require '../include/utils.inc.php';

top_httphead();
header("Content-Type: text/json");

$copyID = GETPOST("id", 'int');

if (!$copyID)
{
  echo '{ "msg": "update(): Missing or empty id" }';
}
else
{
  $eii = new EaEstimateItemMaterial($db);
  if ($eii->fetch($copyID)) 
  {
    $ei = new EaEstimateItem($db);
    $newItemId = $ei->duplicate($eii->estimateitemid);
    if ($newItemId > 0)
    {
      $newMaterialId = $eii->duplicate($copyID, $newItemId);
      if ($newMaterialId > 0)
      {
        echo '{ "msg": "OK", "ID": ' . $newMaterialId . ' }';
      }
      else
      {
        echo '{ "msg": "Create failed: ' . $eii->error . '"}';
      }
    }
    else
    {
      echo '{ "msg": "Create failed: ' . $ei->error . '"}';
    }
  }
  else
    echo '{ "msg": "Copy failed: ' . $ei->error . '" }';
}
