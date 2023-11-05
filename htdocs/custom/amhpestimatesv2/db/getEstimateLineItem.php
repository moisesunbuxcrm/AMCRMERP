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
$sql = "CALL get_estimate_item(".$id.")";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        if ($obj = $db->fetch_object($resql))
        {
            echo '{'.
                '"id":"' . $obj->id . '",'.
                '"estimateid":"' . cleanTxt($obj->estimateid) . '",'.
                '"itemno":"' . cleanTxt($obj->itemno) . '",'.
                '"itemtype": "' . cleanTxt($obj->itemtype) . '",' .
                '"modtype": "' . cleanTxt($obj->modtype) . '",' .
                '"wintype": "' . cleanTxt($obj->wintype) . '",' .
                '"name":"' . cleanTxt($obj->name) . '",'.
                '"image":"' . cleanTxt($obj->image) . '",'.
                '"color":"' . cleanTxt($obj->color) . '",'.
                '"cost_price":"' . cleanTxt($obj->cost_price) . '",'.
                '"sales_price":"' . cleanTxt($obj->sales_price) . '",'.
                '"sales_discount":"' . cleanTxt($obj->sales_discount) . '",'.
                '"inst_price":"' . cleanTxt($obj->inst_price) . '",'.
                '"inst_discount":"' . cleanTxt($obj->inst_discount) . '",'.
                '"otherfees":"' . cleanTxt($obj->otherfees) . '",'.
                '"finalprice":"' . cleanTxt($obj->finalprice) . '",' .
                '"quantity":"' . cleanTxt($obj->quantity) . '"' .
            '}';
            $db->free($resql);
            $db->db->next_result(); // Stored procedure returns an extra result set :(
            return 1;
        }
    }
    $db->free($resql);
}

$db->db->next_result(); // Stored procedure returns an extra result set :(

echo '{ "err": "Unknown Estimate Line Item id: ' . $id . '" }';
