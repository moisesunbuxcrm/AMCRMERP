<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item.class.php';

top_httphead();
header("Content-Type: text/json");

$eDB = new EaEstimateItem($db);

$eDB->estimateid=GETPOST("estimateid", 'int');
$eDB->itemno=GETPOST("itemno", 'int');
$eDB->itemtype=GETPOST("itemtype", 'alpha');
$eDB->modtype=GETPOST("modtype", 'alpha');
$eDB->wintype=GETPOST("wintype", 'alpha');
$eDB->name=GETPOST("name", 'alpha');
$eDB->image=GETPOST("image", 'alpha');
$eDB->color=GETPOST("color", 'alpha');
$eDB->cost_price=GETPOST("cost_price", 'int');
$eDB->sales_price=GETPOST("sales_price", 'int');
$eDB->sales_discount=GETPOST("sales_discount", 'int');
$eDB->inst_price=GETPOST("inst_price", 'int');
$eDB->inst_discount=GETPOST("inst_discount", 'int');
$eDB->otherfees=GETPOST("otherfees", 'int');
$eDB->finalprice=GETPOST("finalprice", 'int');
$eDB->quantity=GETPOST("quantity", 'int');

if ($eDB->create() == 1)
{
  echo '{ "msg": "OK", "id": ' . $eDB->id . ' }';
}
else{
  echo '{ "msg": "Create failed: ' . $eDB->error . '"}';
}