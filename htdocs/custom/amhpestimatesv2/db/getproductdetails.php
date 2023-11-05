<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require '../include/utils.inc.php';

top_httphead();
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/json");

$id = GETPOST("id", 'int');
$sql = "CALL get_product_details(".$id.")";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        if ($obj = $db->fetch_object($resql))
        {
            $obj->id = $obj->rowid;
            echo json_encode($obj);
            $db->free($resql);
            $db->db->next_result(); // Stored procedure returns an extra result set :(
            return 1;
        }
    }
    $db->free($resql);
}

$db->db->next_result(); // Stored procedure returns an extra result set :(

echo '{ "msg": "get_product_details(): Failed: ' . $db->lasterror() . '" }';
