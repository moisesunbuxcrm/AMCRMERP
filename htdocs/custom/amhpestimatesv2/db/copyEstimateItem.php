<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item.class.php';
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
  $eDB = new EaEstimateItem($db);
  $newID = $eDB->duplicate($copyID);
  if ($newID > 0)
  {
    echo '{ "msg": "OK", "newID": ' . $newID . ' }';
  }
  else
  {
    echo '{ "msg": "Create failed: ' . $eDB->error . '"}';
  }
}