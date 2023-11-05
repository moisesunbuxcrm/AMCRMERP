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
header("Content-Type: text/json");

$sql = "CALL getThirdPartiesForEstimate()";
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
                '"rowid":"' . $obj->rowid . '",'.
                '"CUSTOMERNAME":"' . cleanTxt($obj->CUSTOMERNAME) . '",'.
                '"CONTACTNAME":"' . cleanTxt($obj->CONTACTNAME) . '",'.
                '"CONTACTPHONE1":"' . cleanTxt($obj->CONTACTPHONE1) . '",'.
                '"CONTACTPHONE2":"' . cleanTxt($obj->CONTACTPHONE2) . '",'.
                '"CUSTOMERADDRESS":"' . cleanTxt($obj->CUSTOMERADDRESS) . '",'.
                '"ZIPCODE":"' . cleanTxt($obj->ZIPCODE) . '",'.
                '"CITY":"' . cleanTxt($obj->CITY) . '",'.
                '"STATE":"' . cleanTxt($obj->STATE) . '",'.
                '"PHONENUMBER1":"' . cleanTxt($obj->PHONENUMBER1) . '",'.
                '"PHONENUMBER2":"' . cleanTxt($obj->PHONENUMBER2) . '",'.
                '"FAXNUMBER":"' . cleanTxt($obj->FAXNUMBER) . '",'.
                '"EMail":"' . cleanTxt($obj->EMail) . '"'.
                '}';
            $isFirst=false;
        }
        echo ']';
    }
    $db->free($resql);
    $db->db->next_result(); // Stored procedure returns an extra result set :(
    return 1;
}

echo '{ "msg": "getThirdPartiesForEstimate(): Failed: ' . $poDB->error . '" }';
