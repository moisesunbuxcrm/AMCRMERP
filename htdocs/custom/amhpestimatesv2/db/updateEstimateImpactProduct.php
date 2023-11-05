<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate_item_impact.class.php';

top_httphead();
header("Content-Type: text/json");

$id = GETPOST("id", 'int');

if (!$id)
{
  echo '{ "error": "update(): Missing or empty id" }';
}
else
{
  $eii = new EaEstimateItemImpact($db);
  if ($eii->fetch($id)) 
  {
    $ei = new EaEstimateItem($db);
    if ($ei->fetch($eii->estimateitemid))
    {
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
      if ($ei->update(null) == 1) {
        //$eii->estimateitemid=GETPOST("estimateitemid", 'alpha'); // must not change
        $eii->provider=GETPOST("provider", 'alpha');
        $eii->is_def_color=GETPOST("is_def_color", 'alpha')=='true';
        $eii->is_def_glass_color=GETPOST("is_def_glass_color", 'alpha')=='true';
        $eii->is_standard=GETPOST("is_standard", 'alpha')=='true';
        $eii->roomtype=GETPOST("roomtype", 'number');
        $eii->roomnum=GETPOST("roomnum", 'alpha');
        $eii->floornum=GETPOST("floornum", 'alpha');
        $eii->product_ref=GETPOST("product_ref", 'alpha');
        $eii->configuration=GETPOST("configuration", 'alpha');
        $eii->is_screen=GETPOST("is_screen", 'alpha')=='true';
        $eii->frame_color=GETPOST("frame_color", 'alpha');
        $eii->is_colonial=GETPOST("is_colonial", 'alpha')=='true';
        $eii->colonial_fee=GETPOST("colonial_fee", 'number');
        $eii->colonial_across=GETPOST("colonial_across", 'number');
        $eii->colonial_down=GETPOST("colonial_down", 'number');
        $eii->width=GETPOST("width", 'number');
        $eii->widthtxt=GETPOST("widthtxt", 'alpha');
        $eii->height=GETPOST("height", 'number');
        $eii->heighttxt=GETPOST("heighttxt", 'alpha');
        $eii->length=GETPOST("length", 'number');
        $eii->lengthtxt=GETPOST("lengthtxt", 'alpha');
        $eii->glass_type=GETPOST("glass_type", 'alpha');
        $eii->glass_color=GETPOST("glass_color", 'alpha');
        $eii->interlayer=GETPOST("interlayer", 'alpha');
        $eii->coating=GETPOST("coating", 'alpha');
        $eii->room_description=GETPOST("room_description", 'alpha');
        if ($eii->update() == 1)
          echo '{ "msg": "OK" }';
        else
          echo '{ "error": "Update failed: ' . $eii->error . '" }';
      }
      else
        echo '{ "error": "Update failed: ' . $ei->error . '" }';
    }
    else
      echo '{ "error": "Update failed: ' . $ei->error . '" }';
  }
  else
    echo '{ "error": "Update failed: ' . $ei->error . '" }';
}

