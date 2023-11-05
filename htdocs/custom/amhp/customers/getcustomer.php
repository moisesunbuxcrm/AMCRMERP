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
dol_syslog(join(',',$_GET));

$return_arr = array();

$id = $_GET['id']?$_GET['id']:'';
$sql = "SELECT *";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql.= " WHERE s.rowid = " . $db->escape($id);
$customer = new Societe($db);

$resql=$db->query($sql);
if ($resql)
{
	while ($row = $db->fetch_array($resql))
	{			
		array_push($return_arr,$row);
	}

	echo json_encode($return_arr);
}
else
{
	echo json_encode(array('nom'=>'Error','label'=>'Error','key'=>'Error','value'=>'Error'));
}
