<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))	define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))	define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))	define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))	 define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))		define('NOCSRFCHECK','1');

require '../../main.inc.php';

top_httphead();
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
			$val->name = $obj->name;
			array_push($resultsarray,$val);
		}
	}
	else
	{
		echo $db->lasterror();
	}

	return $resultsarray;
}

function getStackingCharts($db)
{
	$sql = "SELECT id as value, name as name, SQFEETPRICE FROM llx_ea_stackingcharts WHERE active=1 order by name";
	$resultsarray = array();
	
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($obj = $db->fetch_object($resql))
		{
			$val = new stdClass();
			$val->value = (int) $obj->value;
			$val->name = $obj->name;
			$val->SQFEETPRICE = (float) $obj->SQFEETPRICE;
			array_push($resultsarray,$val);
		}
	}
	else
	{
		echo $db->lasterror();
	}

	return $resultsarray;
}

function getStackingData($db)
{
	$resultsarray = array();
	$test = 1;
	
	$sql = "SELECT c.name, `BLADES`, `MO`, `STACK`, `TRACK` FROM `llx_ea_stackingdata` d left join `llx_ea_stackingcharts` c on d.chartid = c.id WHERE d.active=1 and c.active=1";
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($obj = $db->fetch_object($resql))
		{
			$cname = $obj->name;
			if (! $resultsarray[$cname])
			{
				$resultsarray[$cname] = array();
			}

			$val = new stdClass();
			$val->blades = (float) $obj->BLADES;
			$val->mo = (float) $obj->MO;
			$val->stack = (float) $obj->STACK;
			$val->track = (float) $obj->TRACK;

			$resultsarray[$cname][strval($val->mo)] = $val;
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

	$array['Name'] = $user->firstname." ".$user->lastname;
	$array['admin']= $user->admin == 1;

	return $array;
}

$estimatedata["user"] = getUser();
$estimatedata["colors"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_colors WHERE active=1 order by name");
$estimatedata["producttypes"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_producttypes WHERE active=1 order by name");
$estimatedata["materials"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_materials WHERE active=1 order by name");
$estimatedata["windowtypes"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_windowtypes WHERE active=1 order by name");
$estimatedata["itemtypes"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_itemtypes WHERE active=1 order by name");
$estimatedata["mounts"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_mounts WHERE active=1 order by name");
$estimatedata["angulartypes"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_angulartypes WHERE active=1 order by name");
$estimatedata["locksizes"] = getDropdownValues($db, "SELECT id as value, size as name FROM llx_ea_locksizes WHERE active=1 order by size");
$estimatedata["lockins"] = getDropdownValues($db, "SELECT id as value, name as name FROM llx_ea_lockins WHERE active=1 order by name");
$estimatedata["stackingcharts"] = getStackingCharts($db);
$estimatedata["stackingdata"] = getStackingData($db);
$estimatedata["defaultvalues"] = getDefaultValues();

echo "var estimateData = " . json_encode($estimatedata) . ";\n";
echo <<<END
estimateData.getDefaultValue = function(param, def)
{
	if (this.defaultvalues[param] == undefined)
		return def;
	return this.defaultvalues[param];
};
END;

echo <<<END
estimateData.getDefaultBool = function(param, def)
{
	if (this.defaultvalues[param] == undefined)
		return def;
	var v = this.defaultvalues[param]; 
	if (v.toLowerCase)
		v = v.toLowerCase();
	switch(v){
		case true:
		case "true":
		case 1:
		case "1":
		case "on":
		case "yes":
		case "y":
			return true;
		default: 
			return false;
	}
};
END;
