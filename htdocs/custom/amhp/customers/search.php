<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

top_httphead();
header("Content-Type: text/json");

$return_arr = array();

$query = $_GET['q']?$_GET['q']:'';
$socid = $_GET['id']?$_GET['id']:'';
$sql = "SELECT s.*, d.code_departement as state_code";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as d ON s.fk_departement = d.rowid";
$sql.= " WHERE s.entity IN (".getEntity('societe').")";
if ($query)
{
	$sql.=" AND (";
	$sql.="s.nom LIKE '%" . $db->escape($query) . "%'";
	$sql.=")";
}
if ($socid)
{
	$sql.=" AND ";
	$sql.="s.rowid = " . $db->escape($socid);
}
$sql.= " ORDER BY s.nom ASC";

$customer = new Societe($db);

$resql=$db->query($sql);
if ($resql)
{
	while (($row = $db->fetch_array($resql)) && count($return_arr)<100)
	{
		$customer->id=$row["rowid"];
		$customer->name=$row["name"];
		$customer->code_client = $row["code_client"];
		$customer->status=$row["status"];
		$row['statusIcon']=$customer->getLibStatut(3);
			
		array_push($return_arr,$row);
	}

	echo json_encode($return_arr);
}
else
{
	echo json_encode(array('nom'=>'Error','label'=>'Error','key'=>'Error','value'=>'Error'));
}
