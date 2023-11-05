<?php
if (! defined('NOTOKENRENEWAL'))define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))	define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))	define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))	define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))	define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))	define('NOCSRFCHECK','1');

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

top_httphead();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/javascript");

$estimatedata = array();

function getDropdownValues($db, $sql)
{
	$resultsarray = array();
	
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($obj = $db->fetch_object($resql))
		{
			$val = new stdClass();
			$val->value = $obj->value;
			$val->label = $obj->label;
			array_push($resultsarray,$val);
		}
	}
	else
	{
		echo $db->lasterror();
	}

	return $resultsarray;
}

function getDefaultValues()
{
    global $user,$conf;
	$array = array();

	$array['PERMIT'] = $conf->global->AMHP_DEFAULT_PERMIT;
	$array['INSTTIME'] = $conf->global->AMHP_DEFAULT_INSTTIME;
	$array['CHECK50'] = $conf->global->AMHP_DEFAULT_CHECK50;
	$array['SIGNATUREREQ'] = $conf->global->AMHP_DEFAULT_SIGNATUREREQ;
	$array['YEARSWARRANTY'] = $conf->global->AMHP_DEFAULT_YEARSWARRANTY;
	$array['CHECK10YEARSWARRANTY'] = $conf->global->AMHP_DEFAULT_CHECK10YEARSWARRANTY;
	$array['LIFETIMEWARRANTY'] = $conf->global->AMHP_DEFAULT_LIFETIMEWARRANTY;
	$array['CHECKFREEOPENINGCLOSING'] = $conf->global->AMHP_DEFAULT_CHECKFREEOPENINGCLOSING;
	$array['CHECKNOPAYMENT'] = $conf->global->AMHP_DEFAULT_CHECKNOPAYMENT;
	$array['PO_TEMPLATE'] = $conf->global->AMHP_DEFAULT_PO_TEMPLATE;
	$array['ESTHTVALUE'] = $conf->global->AMHP_DEFAULT_ESTHTVALUE;
	$array['CHECK10YEARSFREEMAINTENANCE'] = $conf->global->AMHP_DEFAULT_CHECK10YEARSFREEMAINTENANCE;
	$array['HTVALUE'] = $conf->global->AMHP_DEFAULT_HTVALUE;
	$array['MATERIAL'] = $conf->global->AMHP_DEFAULT_MATERIAL;
	$array['COLOR'] = $conf->global->AMHP_DEFAULT_COLOR;
	$array['PROVIDERID'] = $conf->global->AMHP_DEFAULT_PROVIDER_ID;
	$array['LOCKIN'] = $conf->global->AMHP_DEFAULT_LOCKIN;
	$array['LOCKSIZE'] = $conf->global->AMHP_DEFAULT_LOCKSIZE;
	$array['SQFEETPRICE'] = $conf->global->AMHP_DEFAULT_SQFEETPRICE;

    return $array;
}

function getUser()
{
    global $user;
	$array = array();

	$array['name'] =  trim($user->firstname." ".$user->lastname);
	$array['admin']= $user->admin == 1;
	$array['phone']= $user->user_mobile;

	return $array;
}

function getCompany()
{
	global $conf; 

	$array = array();
	$array['name'] = trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_NOM));
	$array['address'] = trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_ADDRESS));
	$array['town']= trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_TOWN));
	$array['zip']= trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_ZIP));
	$array['state']= trim(strtoupper(getState($conf->global->MAIN_INFO_SOCIETE_STATE, 2)));
	$array['phone'] = trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_TEL));
	$array['fax'] = trim(strtoupper($conf->global->MAIN_INFO_SOCIETE_FAX));
	
	return $array;
}

$estimatedata["user"] = getUser();
$estimatedata["company"] = getCompany();
$estimatedata["colors"] = getDropdownValues($db, "SELECT id as value, name as label FROM llx_ea_colors WHERE active=1 order by name");
$estimatedata["providers"] = getDropdownValues($db, "SELECT id as value, name as label FROM llx_ea_stackingcharts WHERE active=1 order by name");
$estimatedata["roomtypes"] = getDropdownValues($db, "SELECT id as value, name as label FROM llx_ea_rooms WHERE active=1 order by name");

$estimatedata["defaultvalues"] = getDefaultValues();

echo json_encode($estimatedata);
