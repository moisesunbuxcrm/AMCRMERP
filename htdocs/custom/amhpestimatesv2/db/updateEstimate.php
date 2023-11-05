<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate.class.php';
require '../include/utils.inc.php';

top_httphead();
header("Content-Type: text/json");

$id = GETPOST("id", 'int');

if (!$id)
{
  echo '{ "msg": "update(): Missing or empty id" }';
}
else
{
  $eDB = new EaEstimate($db);
  if ($eDB->fetch($id))
  {
    $eDB->estimatenum = GETPOST("estimatenum", 'alpha');
    $eDB->quotedate = GETPOST("quotedate", 'alpha');
    $eDB->customerid = GETPOST("customerid", 'int');
    $eDB->folio = GETPOST("folio", 'alpha');

    $eDB->deposit_percent = GETPOST("deposit_percent", 'int');
    $eDB->deposit_percent_with_install = GETPOST("deposit_percent_with_install", 'int');
    $eDB->percent_final_inspection = GETPOST("percent_final_inspection", 'int');
    $eDB->warranty_years = GETPOST("warranty_years", 'int');
    $eDB->pay_upon_completion = GETPOST("pay_upon_completion", 'alpha')=='true';
    $eDB->new_construction_owner_responsability = GETPOST("new_construction_owner_responsability", 'alpha')=='true';
    $eDB->status = GETPOST("status", 'alpha');
    $eDB->status_reason = GETPOST("status_reason", 'alpha');
    $eDB->approved_date = GETPOST("approved_date", 'alpha');
    $eDB->rejected_date = GETPOST("rejected_date", 'alpha');
    $eDB->delivered_date = GETPOST("delivered_date", 'alpha');
    $eDB->permitId = GETPOST("permitId", 'int');

    $eDB->vendor = GETPOST("vendor", 'alpha');
    $eDB->vendor_phone = GETPOST("vendor_phone", 'alpha');
    $eDB->defcolor = GETPOST("defcolor", 'alpha');
    $eDB->defglasscolor = GETPOST("defglasscolor", 'alpha');
    $eDB->is_alteration = GETPOST("is_alteration", 'alpha')=='true';
    $eDB->is_installation_included = GETPOST("is_installation_included", 'alpha')=='true';
    
    $eDB->add_sales_discount = GETPOST("add_sales_discount", 'int');
    $eDB->add_inst_discount = GETPOST("add_inst_discount", 'int');
    $eDB->permits = GETPOST("permits", 'int');
    $eDB->salestax = GETPOST("salestax", 'int');
    $eDB->totalprice = GETPOST("totalprice", 'int');
    $eDB->notes = GETPOST("notes", 'alpha');
    $eDB->public_notes = GETPOST("public_notes", 'alpha');
            
    if ($eDB->update() == 1)
    {
      echo '{'.
        '"id":"' . $eDB->id . '",'.
        '"estimatenum":"' . cleanTxt($eDB->estimatenum) . '",' .
        '"quotedate":"' . cleanTxt($eDB->quotedate) . '",' .
        '"vendor":"' . cleanTxt($eDB->vendor) . '",' .
        '"vendor_phone":"' . cleanTxt($eDB->vendor_phone) . '",' .
        '"defcolor":"' . cleanTxt($eDB->defcolor) . '",' .
        '"defglasscolor":"' . cleanTxt($eDB->defglasscolor) . '",' .
        '"is_alteration":"' . cleanTxt($eDB->is_alteration) . '",' .
        '"is_installation_included":"' . cleanTxt($eDB->is_installation_included) . '",' .
        '"customerid":"' . cleanTxt($eDB->customerid) . '",'.
        '"folio":"' . cleanTxt($eDB->folio) . '",'.

        '"deposit_percent":' . cleanTxt($eDB->deposit_percent) . ','.
        '"deposit_percent_with_install":' . cleanTxt($eDB->deposit_percent_with_install) . ','.
        '"percent_final_inspection":' . cleanTxt($eDB->percent_final_inspection) . ','.
        '"warranty_years":' . cleanTxt($eDB->warranty_years) . ','.
        '"pay_upon_completion":"' . cleanTxt($eDB->pay_upon_completion) . '",'.
        '"new_construction_owner_responsability":"' . cleanTxt($eDB->new_construction_owner_responsability) . '",'.
        '"status":"' . cleanTxt($eDB->status) . '",'.
        '"status_reason":"' . cleanTxt($eDB->status_reason) . '",'.
        '"approved_date":"' . cleanTxt($eDB->approved_date) . '",'.
        '"rejected_date":"' . cleanTxt($eDB->rejected_date) . '",'.
        '"delivered_date":"' . cleanTxt($eDB->delivered_date) . '",'.
        ($eDB->permitId != '' ? '"permitId": ' . cleanTxt($eDB->permitId) . ',' : '').

        '"add_sales_discount":' . cleanTxt($eDB->add_sales_discount) . ','.
        '"add_inst_discount":' . cleanTxt($eDB->add_inst_discount) . ','.
        '"permits":' . cleanTxt($eDB->permits) . ','.
        '"salestax":' . cleanTxt($eDB->salestax) . ','.
        '"totalprice":' . cleanTxt($eDB->totalprice) . ','.
        '"notes":"' . cleanQuotes($eDB->notes) . '",'.
        '"public_notes":"' . cleanQuotes($eDB->public_notes) . '",'.
        '"customer":{'.
          '"id":"' . cleanTxt($eDB->customerid) . '",'.
          '"customername":"' . cleanTxt($eDB->customername) . '",'.
          '"contactname":"' . cleanTxt($eDB->contactname) . '",'.
          '"contactphone":"' . cleanTxt($eDB->contactphone) . '",'.
          '"contactmobile":"' . cleanTxt($eDB->contactmobile) . '",'.
          '"contactaddress":"' . cleanTxt($eDB->contactaddress) . '",'.
          '"customeraddress":"' . cleanTxt($eDB->customeraddress) . '",'.
          '"customerzip":"' . cleanTxt($eDB->customerzip) . '",'.
          '"customercity":"' . cleanTxt($eDB->customercity) . '",'.
          '"customerstate":"' . cleanTxt($eDB->customerstate) . '",'.
          '"customerphone":"' . cleanTxt($eDB->customerphone) . '",'.
          '"customermobile":"' . cleanTxt($eDB->customermobile) . '",'.
          '"customeremail":"' . cleanTxt($eDB->customeremail) . '",'.
          '"folionumber":"' . cleanTxt($eDB->folionumber) . '",'.
          '"reltype":"' . cleanTxt($eDB->reltype) . '"'.
          '}'.
        '}';
    }
    else
      echo '{ "msg": "Update failed: ' . $eDB->error . '" }';
  }
  else
  {
    if ($eDB->error)
      echo '{ "msg": "Update failed: ' . $eDB->error . '" }';
    else
      echo '{ "msg": "Update failed: Unknown id=' . $id . '" )';
  }
}

