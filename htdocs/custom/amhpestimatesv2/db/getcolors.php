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

$sql = "CALL get_colors()";
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
            echo '"' . cleanTxt($obj->name) . '"';
            $isFirst=false;
        }
        echo ']';
    }
    $db->free($resql);
    $db->db->next_result(); // Stored procedure returns an extra result set :(
}
else
    echo '{ "msg": "get_colors(): Failed: ' . $db->lasterror() . '" }';
