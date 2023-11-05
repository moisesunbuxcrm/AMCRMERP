<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';

top_httphead();
header("Content-Type: text/json");

$eventid = $_GET['e']?$_GET['e']:'';
$userid = $_GET['u']?$_GET['u']:'';

/*
$resql=null;
if ($eventid && $userid)
{
	$sql = "UPDATE ".MAIN_DB_PREFIX."actioncomm ";
	$sql.= " SET fk_user_action=".$userid;
	$sql.= " WHERE id=".$eventid;
	$sql.= " AND ".$userid." in (";
	$sql.= "     SELECT fk_element";
	$sql.= "        FROM ".MAIN_DB_PREFIX."actioncomm_resources";
	$sql.= "        WHERE fk_actioncomm = ".$eventid;
	$sql.= "        AND element_type = 'user'";
	$sql.= "     )";

	$resql=$db->query($sql);
}

if ($resql)
{
	echo json_encode(array('result'=>$db->affected_rows($resql),'sql'=>$sql,'sess'=>$_SESSION['assignedtouser']));
}
else
{
	echo json_encode(array('result'=>$db->lasterror()));
}
*/

// No need to change database - in fact it doesn't work because card.php will overwrite it.
// We just need to update the Session variable (and then submit the form from javascript)

if (! empty($_SESSION['assignedtouser']))
{
	$listofuserid=json_decode($_SESSION['assignedtouser'], true);
	$listofuserid = array($userid => $listofuserid[$userid]) + $listofuserid;
	$_SESSION['assignedtouser'] = json_encode($listofuserid);
	echo $_SESSION['assignedtouser'];
}
else
{
	echo 'noop';
}
