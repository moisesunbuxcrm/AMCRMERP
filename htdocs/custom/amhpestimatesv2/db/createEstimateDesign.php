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

$ei = new EaEstimateItem($db);
$ei->estimateid=GETPOST("estimateid", 'int');
$ei->itemno=GETPOST("itemno", 'alpha');
$ei->itemtype=GETPOST("itemtype", 'alpha');
$ei->modtype=GETPOST("modtype", 'alpha');
$ei->wintype=GETPOST("wintype", 'alpha');
$ei->name=GETPOST("name", 'alpha');
$ei->image=GETPOST("image", 'alpha');
$ei->color=GETPOST("color", 'alpha');
$ei->cost_price=GETPOST("cost_price", 'number');
$ei->sales_price=GETPOST("sales_price", 'number');
$ei->sales_discount=GETPOST("sales_discount", 'number');
$ei->inst_price=GETPOST("inst_price", 'number');
$ei->inst_discount=GETPOST("inst_discount", 'number');
$ei->otherfees=GETPOST("otherfees", 'number');
$ei->finalprice=GETPOST("finalprice", 'number');
$ei->quantity=GETPOST("quantity", 'number');

if ($ei->create() == 1)
{
  $eii = new EaEstimateItemDesign($db);
  $eii->estimateitemid=$ei->id;
  $eii->provider=GETPOST("provider", 'alpha');
  $eii->product_ref=GETPOST("product_ref", 'alpha');
  $eii->width=GETPOST("width", 'number');
  $eii->widthtxt=GETPOST("widthtxt", 'alpha');
  $eii->height=GETPOST("height", 'number');
  $eii->heighttxt=GETPOST("heighttxt", 'alpha');
  
  if ($eii->create() == 1)
  {
      echo '{ 
        "estimateitemid": ' . $ei->id . ', 
        "id": ' . $eii->id . ' 
      }';
  }
  else{
    echo '{ "error": "Create failed: ' . $eii->error . '"}';
  }
}
else{
  echo '{ "error": "Create failed: ' . $ei->error . '"}';
}
