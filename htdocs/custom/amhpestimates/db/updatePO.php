<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';

top_httphead();
header("Content-Type: text/json");

$id = GETPOST("POID", 'int');

if (!$id)
{
  echo '{ "msg": "update(): Missing or empty POID" }';
}
else
{
  $poDB = new EaProductionOrders($db);
  if ($poDB->fetch($id))
  {
    $poDB->PONUMBER = GETPOST("PONUMBER", 'alpha');
    $poDB->PODATE = GETPOST("PODATE", 'alpha');
    $poDB->QUOTEDATE = GETPOST("QUOTEDATE", 'alpha');
    $poDB->Salesman = GETPOST("Salesman", 'alpha');
    $poDB->COLOR = GETPOST("COLOR", 'alpha');
    $poDB->HTVALUE = GETPOST("HTVALUE", 'int');
    $poDB->DESCRIPTIONOFWORK = GETPOST("DESCRIPTIONOFWORK");
    $poDB->OBSERVATION = GETPOST("OBSERVATION");
    $poDB->OBSINST = GETPOST("OBSINST");
    $poDB->SQINSTPRICE = GETPOST("SQINSTPRICE", 'int');
    $poDB->INSTSALESPRICE = GETPOST("INSTSALESPRICE", 'int');
    $poDB->ESTHTVALUE = GETPOST("ESTHTVALUE", 'int');
    $poDB->ESTOBSERVATION = GETPOST("ESTOBSERVATION");
    $poDB->INSTTIME = GETPOST("INSTTIME", 'int');
    $poDB->PERMIT = GETPOST("PERMIT", 'int');
    $poDB->CUSTVALUE = GETPOST("CUSTVALUE", 'int');
    $poDB->CUSTOMIZE = GETPOST("CUSTOMIZE", 'int');
    $poDB->SALES_TAX = GETPOST("SALES_TAX", 'int');
    $poDB->SALESTAXAMOUNT = GETPOST("SALESTAXAMOUNT", 'int');
    $poDB->TOTALALUM = GETPOST("TOTALALUM", 'int');
    $poDB->SALESPRICE = GETPOST("SALESPRICE", 'int');
    $poDB->SQFEETPRICE = GETPOST("SQFEETPRICE", 'int');
    $poDB->OTHERFEES = GETPOST("OTHERFEES", 'int');
    $poDB->Check50 = GETPOST("Check50", 'alpha')=='true';
    $poDB->CheckAssIns = GETPOST("CheckAssIns", 'alpha')=='true';
    $poDB->OrderCompleted = GETPOST("OrderCompleted", 'alpha')=='true';
    $poDB->Check10YearsWarranty = GETPOST("Check10YearsWarranty", 'alpha')=='true';
    $poDB->Check10YearsFreeMaintenance = GETPOST("Check10YearsFreeMaintenance", 'alpha')=='true';
    $poDB->CheckFreeOpeningClosing = GETPOST("CheckFreeOpeningClosing", 'alpha')=='true';
    $poDB->CheckNoPayment = GETPOST("CheckNoPayment", 'alpha')=='true';
    $poDB->YearsWarranty = GETPOST("YearsWarranty", 'int');
    $poDB->LifeTimeWarranty = GETPOST("LifeTimeWarranty", 'alpha')=='true';
    $poDB->SignatureReq = GETPOST("SignatureReq", 'alpha')=='true';
    $poDB->Discount = GETPOST("Discount", 'int');
    $poDB->TOTALTRACK = GETPOST("TOTALTRACK", 'int');
    $poDB->TAPCONS = GETPOST("TAPCONS", 'int');
    $poDB->TOTALLONG = GETPOST("TOTALLONG", 'int');
    $poDB->FASTENERS = GETPOST("FASTENERS", 'int');
    $poDB->TOTALALUMINST = GETPOST("TOTALALUMINST", 'int');
    $poDB->TOTALLINEARFT = GETPOST("TOTALLINEARFT", 'int');
    $poDB->customerId = GETPOST("customerId", 'int');

    if ($poDB->update() == 1)
      echo '{ "msg": "OK" }';
    else
      echo '{ "msg": "Update failed: ' . $poDB->error . '" }';
  }
  else
  {
    if ($poDB->error)
      echo '{ "msg": "Update failed: ' . $poDB->error . '" }';
    else
      echo '{ "msg": "Update failed: Unknown POID=' . $id . '" )';
  }
}

