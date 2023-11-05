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
header("Content-Type: text");
//header("Content-Disposition: inline");
header("Content-Disposition: attachment; filename=products.csv");

$id = GETPOST("id",'int');
$sql = "CALL get_product_details_csv()";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        echo ''
            . '"id",'
            . '"name",'
            . '"ref",'
            . '"description",'
            . '"price",'
            . '"inst_price",'
            . '"cost_price",'
            . '"width",'
            . '"widthtxt",'
            . '"height",'
            . '"heighttxt",'
            . '"length",'
            . '"lengthtxt",'
            . '"itemtype",'
            . '"modtype",'
            . '"configuration",'
            . '"provider",'
            . '"color",'
            . '"wintype",'
            . '"screen",'
            . '"frame_color",'
            . '"glass_type",'
            . '"glass_color",'
            . '"interlayer",'
            . '"coating"'
            . PHP_EOL;
        
        while ($obj = $db->fetch_object($resql))
        {
            echo ''
            . $obj->rowid . ','
            . '"' . cleanTxt($obj->name) . '",'
            . '"' . cleanTxt($obj->ref) . '",'
            . '"' . cleanTxt($obj->description) . '",'
            . (is_null($obj->price) ? 0 : $obj->price) . ','
            . (is_null($obj->inst_price) ? 0 : $obj->inst_price) . ','
            . (is_null($obj->cost_price) ? 0 : $obj->cost_price) . ','
            . (is_null($obj->width) ? 0 : $obj->width) . ','
            . '"' . cleanTxt($obj->widthtxt) . '",'
            . (is_null($obj->height) ? 0 : $obj->height) . ','
            . '"' . cleanTxt($obj->heighttxt) . '",'
            . (is_null($obj->length) ? 0 : $obj->length) . ','
            . '"' . cleanTxt($obj->lengthtxt) . '",'
            . '"' . cleanTxt($obj->itemtype) . '",'
            . '"' . cleanTxt($obj->modtype) . '",'
            . '"' . cleanTxt($obj->configuration) . '",'
            . '"' . cleanTxt($obj->provider) . '",'
            . '"' . cleanTxt($obj->color) . '",'
            . '"' . cleanTxt($obj->wintype) . '",'
            . '"' . cleanTxt($obj->screen) . '",'
            . '"' . cleanTxt($obj->frame_color) . '",'
            . '"' . cleanTxt($obj->glass_type) . '",'
            . '"' . cleanTxt($obj->glass_color) . '",'
            . '"' . cleanTxt($obj->interlayer) . '",'
            . '"' . cleanTxt($obj->coating) . '"'
            . PHP_EOL;
        }
    }
    $db->free($resql);
    $db->db->next_result(); // Stored procedure returns an extra result set :(
    return 1;
}

echo '{ "msg": "get_product_details_csv(): Failed: ' . $db->lasterror() . '" }';
