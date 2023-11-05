<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))	define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))	define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))	define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))	 define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))		define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';

top_httphead();
header("Content-Type: text/json");

$return_arr = array();
$page = $_GET['pg'] && $_GET['pg']!='-1'?$_GET['pg']:null;
$poid = $_GET['po'] ? $_GET['po'] : null;
$nextpo = $_GET['nextpo'] ? $_GET['nextpo'] : null;
$prevpo = $_GET['prevpo'] ? $_GET['prevpo'] : null;
$custId = $_GET['custid'] ? $_GET['custid'] : null;
$includeItems = $_GET['ii'] == '1';

$poDB = new EaProductionOrders($db);
if ($page)
	$pos = $poDB->fetchPage($page, 5, $custId); 
else if ($poid)
	$pos = $poDB->fetchPageWithPOID($poid, 5, $custId); 
else if ($nextpo)
	$pos = $poDB->fetchPageAfterPOID($nextpo, 5, $custId); 
else if ($prevpo)
	$pos = $poDB->fetchPageBeforePOID($prevpo, 5, $custId); 

if ($pos)
{
	foreach ($pos as $po)
	{
		$obj = array();
		$obj["POID"] = $po->POID;
		$obj["PONUMBER"] = $po->PONUMBER;
		$obj["PODATE"] = $po->PODATE;
		$obj["QUOTEDATE"] = $po->QUOTEDATE;
		$obj["Salesman"] = $po->Salesman;
		$obj["CUSTOMERNAME"] = $po->CUSTOMERNAME;
		$obj["CONTACTNAME"] = $po->CONTACTNAME;
		$obj["CONTACTPHONE1"] = $po->CONTACTPHONE1;
		$obj["CONTACTPHONE2"] = $po->CONTACTPHONE2;
		$obj["CUSTOMERADDRESS"] = $po->CUSTOMERADDRESS;
		$obj["ZIPCODE"] = $po->ZIPCODE;
		$obj["CITY"] = $po->CITY;
		$obj["STATE"] = $po->STATE;
		$obj["PHONENUMBER1"] = $po->PHONENUMBER1;
		$obj["PHONENUMBER2"] = $po->PHONENUMBER2;
		$obj["FAXNUMBER"] = $po->FAXNUMBER;
		$obj["EMail"] = $po->EMail;
		$obj["COLOR"] = $po->COLOR;
		$obj["HTVALUE"] = $po->HTVALUE;
		$obj["DESCRIPTIONOFWORK"] = $po->DESCRIPTIONOFWORK;
		$obj["OBSERVATION"] = $po->OBSERVATION;
		$obj["TOTALTRACK"] = $po->TOTALTRACK;
		$obj["TAPCONS"] = $po->TAPCONS;
		$obj["TOTALLONG"] = $po->TOTALLONG;
		$obj["FASTENERS"] = $po->FASTENERS;
		$obj["TOTALALUMINST"] = $po->TOTALALUMINST;
		$obj["TOTALLINEARFT"] = $po->TOTALLINEARFT;
		$obj["OBSINST"] = $po->OBSINST;
		$obj["SQINSTPRICE"] = $po->SQINSTPRICE;
		$obj["INSTSALESPRICE"] = $po->INSTSALESPRICE;
		$obj["ESTHTVALUE"] = $po->ESTHTVALUE;
		$obj["ESTOBSERVATION"] = $po->ESTOBSERVATION;
		$obj["INSTTIME"] = $po->INSTTIME;
		$obj["PERMIT"] = $po->PERMIT;
		$obj["CUSTVALUE"] = $po->CUSTVALUE;
		$obj["CUSTOMIZE"] = $po->CUSTOMIZE;
		$obj["SALES_TAX"] = $po->SALES_TAX;
		$obj["SALESTAXAMOUNT"] = $po->SALESTAXAMOUNT;
		$obj["TOTALALUM"] = $po->TOTALALUM;
		$obj["SALESPRICE"] = $po->SALESPRICE;
		$obj["SQFEETPRICE"] = $po->SQFEETPRICE;
		$obj["OTHERFEES"] = $po->OTHERFEES;
		$obj["Check50"] = $po->Check50;
		$obj["CheckAssIns"] = $po->CheckAssIns;
		$obj["OrderCompleted"] = $po->OrderCompleted;
		$obj["Check10YearsWarranty"] = $po->Check10YearsWarranty;
		$obj["Check10YearsFreeMaintenance"] = $po->Check10YearsFreeMaintenance;
		$obj["CheckFreeOpeningClosing"] = $po->CheckFreeOpeningClosing;
		$obj["CheckNoPayment"] = $po->CheckNoPayment;
		$obj["YearsWarranty"] = $po->YearsWarranty;
		$obj["LifeTimeWarranty"] = $po->LifeTimeWarranty;
		$obj["SignatureReq"] = $po->SignatureReq;
		$obj["Discount"] = $po->Discount;
		$obj["customerId"] = $po->customerId;
		$obj["invoiceId"] = $po->invoiceId;
		$obj["invoiceLocked"] = $po->invoiceLocked;
		$obj["permitId"] = $po->permitId;

		if ($includeItems)
		{
			$obj["items"] = Array();
			$itemObject=new EaProductionOrderItems($db);
			$items = $itemObject->fetchByPOID($obj["POID"]);
			foreach($items as $item)
			{
				$newItem = Array();
				$newItem["PODescriptionID"] = $item->PODescriptionID;
				$newItem["POID"] = $item->POID;
				$newItem["LineNumber"] = $item->LineNumber;
				$newItem["OPENINGW"] = $item->OPENINGW;
				$newItem["OPENINGHT"] = $item->OPENINGHT;
				$newItem["TRACK"] = $item->TRACK;
				$newItem["TYPE"] = $item->TYPE;
				$newItem["BLADESQTY"] = $item->BLADESQTY;
				$newItem["BLADESSTACK"] = $item->BLADESSTACK;
				$newItem["BLADESLONG"] = $item->BLADESLONG;
				$newItem["LEFT"] = $item->LEFT;
				$newItem["RIGHT"] = $item->RIGHT;
				$newItem["LOCKIN"] = $item->LOCKIN;
				$newItem["LOCKSIZE"] = $item->LOCKSIZE;
				$newItem["UPPERSIZE"] = $item->UPPERSIZE;
				$newItem["UPPERTYPE"] = $item->UPPERTYPE;
				$newItem["LOWERSIZE"] = $item->LOWERSIZE;
				$newItem["LOWERTYPE"] = $item->LOWERTYPE;
				$newItem["ANGULARTYPE"] = $item->ANGULARTYPE;
				$newItem["ANGULARSIZE"] = $item->ANGULARSIZE;
				$newItem["ANGULARQTY"] = $item->ANGULARQTY;
				$newItem["MOUNT"] = $item->MOUNT;
				$newItem["ALUMINST"] = $item->ALUMINST;
				$newItem["LINEARFT"] = $item->LINEARFT;
				$newItem["OPENINGHT4"] = $item->OPENINGHT4;
				$newItem["ALUMINST4"] = $item->ALUMINST4;
				$newItem["EST8HT"] = $item->EST8HT;
				$newItem["ALUM"] = $item->ALUM;
				$newItem["WINDOWSTYPE"] = $item->WINDOWSTYPE;
				$newItem["EXTRAANGULARTYPE"] = $item->EXTRAANGULARTYPE;
				$newItem["EXTRAANGULARSIZE"] = $item->EXTRAANGULARSIZE;
				$newItem["EXTRAANGULARQTY"] = $item->EXTRAANGULARQTY;
				$newItem["SQFEETPRICE"] = $item->SQFEETPRICE;
				$newItem["PRODUCTTYPE"] = $item->PRODUCTTYPE;
				$newItem["COLOR"] = $item->COLOR;
				$newItem["MATERIAL"] = $item->MATERIAL;
				$newItem["PROVIDER"] = $item->PROVIDER;
				$newItem["INSTFEE"] = $item->INSTFEE;
				$newItem["TUBETYPE"] = $item->TUBETYPE;
				$newItem["TUBESIZE"] = $item->TUBESIZE;
				$newItem["TUBEQTY"] = $item->TUBEQTY;
				array_push($obj["items"],$newItem);
			}
		}
		
		array_push($return_arr,$obj);
	}
	echo json_encode($return_arr);
}
else
{
	if ($poDB->error)
		echo $poDB->error;
	else
		echo '[]';
}