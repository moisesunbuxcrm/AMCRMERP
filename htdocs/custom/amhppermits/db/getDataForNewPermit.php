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

$poid = GETPOST("poid", 'int');
$eid = GETPOST("eid", 'int');
$socid = GETPOST("socid", 'int');

if ($poid>0)
  $sql = "CALL getDataForNewPermit(".$poid.")";
elseif ($eid>0)
  $sql = "CALL getDataForNewPermitFromEstimate(".$eid.")";
else
  $sql = "CALL getDataForNewPermitForSocid(".$socid.")";

$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
      $obj = $db->fetch_object($resql);
      print("{");
      $i=0;
      foreach($obj as $key => $val)
      {
        if ($i > 0)
            print(",");
        print("\"$key\":\"$val\"");
        $i++;
      }
      print("}");
    }
    $db->free($resql);
    $db->db->next_result(); // Stored procedure returns an extra result set :(
    return 1;
}

echo '{ "msg": "getDataForNewPermit(): Failed: ' . $db->lasterror . '" }';

