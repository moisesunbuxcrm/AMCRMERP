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

$tpid = GETPOST("tpid", 'int');
$poid = GETPOST("poid", 'int');
$eid = GETPOST("eid", 'int');
$result = 0;
$rowid = 0;
if ($tpid != "" && ($poid != "" || $eid != ""))
{
    if ($poid != "")
        $sql = "CALL createPermit(".$tpid.",".$poid.")";
    else
        $sql = "CALL createPermitFromEstimate(".$tpid.",".$eid.")";

    $resql=$db->query($sql);
    if ($resql) 
    {
        $res = $db->query("SELECT LAST_INSERT_ID() as id " );
		if ($res && $data = $db->fetch_array($res))
		{
            $rowid = $data["id"];
            header('Location: ' . DOL_URL_ROOT ."/custom/amhppermits/buildingpermit_card.php?mainmenu=amhppermits&id=".$rowid);
		}
        else
        {
            $result = 1;
            $msg = "Stored procedure call failed:" . $db->lasterror;
        }
    }
    else
    {
        $result = 1;
        $msg = "Stored procedure call failed:" . $db->lasterror;
    }
}
else
{
    $result = 1;
    $msg = "Missing parameters";
}

if ($result==1)
    echo '{ "msg": "createPermit(): Failed: ' . $msg . '" }';
else
    echo '{ "id": "'.$rowid.'" }';

