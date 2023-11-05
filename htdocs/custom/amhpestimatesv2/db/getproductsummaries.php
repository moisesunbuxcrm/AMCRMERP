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

$sql = "CALL get_product_summaries()";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        echo '[';
        $isFirst=true;
        while ($obj = $db->fetch_object($resql))
        {
            if (!$isFirst)
                echo ',';
            echo '{'.
                '"id":' . $obj->rowid . ','.
                '"name":"' . cleanTxt($obj->name) . '",'.
                '"ref":"' . cleanTxt($obj->ref) . '",'.
                '"provider":"' . cleanTxt($obj->provider) . '",'.
                '"color":"' . cleanTxt($obj->color) . '",'.
                '"itemtype":' . cleanTxt($obj->itemtype) . ','.
                '"modtype":"' . cleanTxt($obj->modtype) . '",'.
                '"wintype":"' . cleanTxt($obj->wintype) . '",'.
                '"glass_color":"' . cleanTxt($obj->glass_color) . '",'.
                '"interlayer":"' . cleanTxt($obj->interlayer) . '",'.
                '"width":"' . cleanTxt($obj->width) . '",'.
                '"widthtxt":"' . cleanTxt($obj->widthtxt) . '",'.
                '"height":"' . cleanTxt($obj->height) . '",'.
                '"heighttxt":"' . cleanTxt($obj->heighttxt) . '",'.
                '"length":"' . cleanTxt($obj->length) . '",'.
                '"lengthtxt":"' . cleanTxt($obj->lengthtxt) . '"'.
                '}';
            $isFirst=false;
        }
        echo ']';
    }
    $db->free($resql);
    $db->db->next_result(); // Stored procedure returns an extra result set :(
    return 1;
}

echo '{ "msg": "get_product_summaries(): Failed: ' . $db->lasterror() . '" }';
