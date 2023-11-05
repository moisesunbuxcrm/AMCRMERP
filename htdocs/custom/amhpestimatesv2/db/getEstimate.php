<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

# The following two lines temporarily remove authentication security for testing new estimates app
#define('NOREQUIREUSER','1');
#define('NOLOGIN','1');

require '../../../main.inc.php';
require '../include/utils.inc.php';

top_httphead();
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/json");

// Get items for this estimate
$itemIDs = array();
$id = GETPOST("id", 'int');
$sql = "CALL get_estimate_items(".$id.")";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        while ($obj = $db->fetch_object($resql))
        {
            $item = array();
            $item["id"] = $obj->id;
            $item["modtype"] = strtoupper($obj->modtype);
            array_push($itemIDs,$item);
        }

    }
    $db->db->next_result(); // Stored procedure returns an extra result set :(
}
else {
    echo '{ "msg": "get_estimate_items(): Failed: ' . $db->lasterror() . '" }';
    return 0;
}

$itemsJSON = array();
// Get item details for this estimate
foreach ($itemIDs as $item) {
    if ($item["modtype"] = "Impact Product") {
        $sql = "CALL get_estimate_item_impact_for(".$item["id"].")";
        $resql=$db->query($sql);
        if ($resql)
        {
            if ($db->num_rows($resql))
            {
                if ($obj = $db->fetch_object($resql))
                {
                    $itemsJSON[$obj->itemno] = 
                        '{'.PHP_EOL.
                        '   "itemno": ' . $obj->itemno . ',' . PHP_EOL .
                        '   "estimateid": "' . cleanTxt($obj->estimateid) . '",' . PHP_EOL .
                        '   "itemtype": "' . cleanTxt($obj->itemtype) . '",' . PHP_EOL .
                        '   "modtype": "' . cleanTxt($obj->modtype) . '",' . PHP_EOL .
                        '   "wintype": "' . cleanTxt($obj->wintype) . '",' . PHP_EOL .
                        '   "name": "' . cleanTxt($obj->name) . '",' . PHP_EOL .
                        '   "image": "' . cleanTxt($obj->image) . '",' . PHP_EOL .
                        '   "color": "' . cleanTxt($obj->color) . '",' . PHP_EOL .
                        '   "cost_price": ' . $obj->cost_price . ',' . PHP_EOL .
                        '   "sales_price": ' . $obj->sales_price . ',' . PHP_EOL .
                        '   "sales_discount": ' . $obj->sales_discount . ',' . PHP_EOL .
                        '   "inst_price": ' . $obj->inst_price . ',' . PHP_EOL .
                        '   "inst_discount": ' . $obj->inst_discount . ',' . PHP_EOL .
                        '   "otherfees": ' . $obj->otherfees . ',' . PHP_EOL .
                        '   "finalprice": ' . $obj->finalprice . ',' . PHP_EOL .
                        '   "quantity": ' . $obj->quantity . ',' . PHP_EOL .

                        '   "id": ' . $obj->id . ','.PHP_EOL.
                        '   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
                        '   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
                        '   "is_def_color": ' . $obj->is_def_color . ',' . PHP_EOL .
                        '   "is_def_glass_color": ' . $obj->is_def_glass_color . ',' . PHP_EOL .
                        '   "is_standard": ' . $obj->is_standard . ',' . PHP_EOL .
                        '   "roomtype": ' . $obj->roomtype . ',' . PHP_EOL .
                        '   "roomnum": ' . $obj->roomnum . ',' . PHP_EOL .
                        '   "floornum": ' . $obj->floornum . ',' . PHP_EOL .
                        '   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
                        '   "configuration": "' . cleanTxt($obj->configuration) . '",' . PHP_EOL .
                        '   "is_screen": ' . $obj->is_screen . ',' . PHP_EOL .
                        '   "frame_color": "' . cleanTxt($obj->frame_color) . '",' . PHP_EOL .
                        '   "is_colonial": ' . $obj->is_colonial . ',' . PHP_EOL .
                        '   "colonial_fee": ' . $obj->colonial_fee . ',' . PHP_EOL .
                        '   "colonial_across": ' . $obj->colonial_across . ',' . PHP_EOL .
                        '   "colonial_down": ' . $obj->colonial_down . ',' . PHP_EOL .
                        '   "width": ' . $obj->width . ',' . PHP_EOL .
                        '   "widthtxt": "' . $obj->widthtxt . '",' . PHP_EOL .
                        '   "height": ' . $obj->height . ',' . PHP_EOL .
                        '   "heighttxt": "' . $obj->heighttxt . '",' . PHP_EOL .
                        '   "length": ' . $obj->length . ',' . PHP_EOL .
                        '   "lengthtxt": "' . $obj->lengthtxt . '",' . PHP_EOL .
                        '   "glass_type": "' . cleanTxt($obj->glass_type) . '",' . PHP_EOL .
                        '   "glass_color": "' . cleanTxt($obj->glass_color) . '",' . PHP_EOL .
                        '   "interlayer": "' . cleanTxt($obj->interlayer) . '",' . PHP_EOL .
                        '   "coating": "' . cleanTxt($obj->coating) . '"' . PHP_EOL .
                        '}';
                }
            }
            $db->free($resql);
        }
        
        $db->db->next_result(); // Stored procedure returns an extra result set :(
    }

    if ($item["modtype"] = "Hardware") {
        $sql = "CALL get_estimate_item_hardware_for(".$item["id"].")";
        $resql=$db->query($sql);
        if ($resql)
        {
            if ($db->num_rows($resql))
            {
                if ($obj = $db->fetch_object($resql))
                {
                    $itemsJSON[$obj->itemno] = 
                        '{'.PHP_EOL.
                        '   "itemno": ' . $obj->itemno . ',' . PHP_EOL .
                        '   "estimateid": "' . cleanTxt($obj->estimateid) . '",' . PHP_EOL .
                        '   "itemtype": "' . cleanTxt($obj->itemtype) . '",' . PHP_EOL .
                        '   "modtype": "' . cleanTxt($obj->modtype) . '",' . PHP_EOL .
                        '   "wintype": "' . cleanTxt($obj->wintype) . '",' . PHP_EOL .
                        '   "name": "' . cleanTxt($obj->name) . '",' . PHP_EOL .
                        '   "image": "' . cleanTxt($obj->image) . '",' . PHP_EOL .
                        '   "color": "' . cleanTxt($obj->color) . '",' . PHP_EOL .
                        '   "cost_price": ' . $obj->cost_price . ',' . PHP_EOL .
                        '   "sales_price": ' . $obj->sales_price . ',' . PHP_EOL .
                        '   "sales_discount": ' . $obj->sales_discount . ',' . PHP_EOL .
                        '   "inst_price": ' . $obj->inst_price . ',' . PHP_EOL .
                        '   "inst_discount": ' . $obj->inst_discount . ',' . PHP_EOL .
                        '   "otherfees": ' . $obj->otherfees . ',' . PHP_EOL .
                        '   "finalprice": ' . $obj->finalprice . ',' . PHP_EOL .
                        '   "quantity": ' . $obj->quantity . ',' . PHP_EOL .

                        '   "id": ' . $obj->id . ','.PHP_EOL.
                        '   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
                        '   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
                        '   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
                        '   "hardwaretype": "' . cleanTxt($obj->hardwaretype) . '",' . PHP_EOL .
                        '   "configuration": "' . cleanTxt($obj->configuration) . '"' . PHP_EOL .
                        '}';
                }
            }
            $db->free($resql);
        }
        
        $db->db->next_result(); // Stored procedure returns an extra result set :(
    }

    if ($item["modtype"] = "Material") {
        $sql = "CALL get_estimate_item_material_for(".$item["id"].")";
        $resql=$db->query($sql);
        if ($resql)
        {
            if ($db->num_rows($resql))
            {
                if ($obj = $db->fetch_object($resql))
                {
                    $itemsJSON[$obj->itemno] = 
                        '{'.PHP_EOL.
                        '   "itemno": ' . $obj->itemno . ',' . PHP_EOL .
                        '   "estimateid": "' . cleanTxt($obj->estimateid) . '",' . PHP_EOL .
                        '   "itemtype": "' . cleanTxt($obj->itemtype) . '",' . PHP_EOL .
                        '   "modtype": "' . cleanTxt($obj->modtype) . '",' . PHP_EOL .
                        '   "wintype": "' . cleanTxt($obj->wintype) . '",' . PHP_EOL .
                        '   "name": "' . cleanTxt($obj->name) . '",' . PHP_EOL .
                        '   "image": "' . cleanTxt($obj->image) . '",' . PHP_EOL .
                        '   "color": "' . cleanTxt($obj->color) . '",' . PHP_EOL .
                        '   "cost_price": ' . $obj->cost_price . ',' . PHP_EOL .
                        '   "sales_price": ' . $obj->sales_price . ',' . PHP_EOL .
                        '   "sales_discount": ' . $obj->sales_discount . ',' . PHP_EOL .
                        '   "inst_price": ' . $obj->inst_price . ',' . PHP_EOL .
                        '   "inst_discount": ' . $obj->inst_discount . ',' . PHP_EOL .
                        '   "otherfees": ' . $obj->otherfees . ',' . PHP_EOL .
                        '   "finalprice": ' . $obj->finalprice . ',' . PHP_EOL .
                        '   "quantity": ' . $obj->quantity . ',' . PHP_EOL .

                        '   "id": ' . $obj->id . ','.PHP_EOL.
                        '   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
                        '   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
                        '   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
                        '   "width": ' . $obj->width . ',' . PHP_EOL .
                        '   "widthtxt": "' . $obj->widthtxt . '",' . PHP_EOL .
                        '   "height": ' . $obj->height . ',' . PHP_EOL .
                        '   "heighttxt": "' . $obj->heighttxt . '",' . PHP_EOL .
                        '   "length": ' . $obj->length . ',' . PHP_EOL .
                        '   "lengthtxt": "' . $obj->lengthtxt . '"' . PHP_EOL .
                        '}';
                }
            }
            $db->free($resql);
        }
        
        $db->db->next_result(); // Stored procedure returns an extra result set :(
    }

    if ($item["modtype"] = "Design") {
        $sql = "CALL get_estimate_item_design_for(".$item["id"].")";
        $resql=$db->query($sql);
        if ($resql)
        {
            if ($db->num_rows($resql))
            {
                if ($obj = $db->fetch_object($resql))
                {
                    $itemsJSON[$obj->itemno] = 
                        '{'.PHP_EOL.
                        '   "itemno": ' . $obj->itemno . ',' . PHP_EOL .
                        '   "estimateid": "' . cleanTxt($obj->estimateid) . '",' . PHP_EOL .
                        '   "itemtype": "' . cleanTxt($obj->itemtype) . '",' . PHP_EOL .
                        '   "modtype": "' . cleanTxt($obj->modtype) . '",' . PHP_EOL .
                        '   "wintype": "' . cleanTxt($obj->wintype) . '",' . PHP_EOL .
                        '   "name": "' . cleanTxt($obj->name) . '",' . PHP_EOL .
                        '   "image": "' . cleanTxt($obj->image) . '",' . PHP_EOL .
                        '   "color": "' . cleanTxt($obj->color) . '",' . PHP_EOL .
                        '   "cost_price": ' . $obj->cost_price . ',' . PHP_EOL .
                        '   "sales_price": ' . $obj->sales_price . ',' . PHP_EOL .
                        '   "sales_discount": ' . $obj->sales_discount . ',' . PHP_EOL .
                        '   "inst_price": ' . $obj->inst_price . ',' . PHP_EOL .
                        '   "inst_discount": ' . $obj->inst_discount . ',' . PHP_EOL .
                        '   "otherfees": ' . $obj->otherfees . ',' . PHP_EOL .
                        '   "finalprice": ' . $obj->finalprice . ',' . PHP_EOL .
                        '   "quantity": ' . $obj->quantity . ',' . PHP_EOL .

                        '   "id": ' . $obj->id . ','.PHP_EOL.
                        '   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
                        '   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
                        '   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
                        '   "width": ' . $obj->width . ',' . PHP_EOL .
                        '   "widthtxt": "' . $obj->widthtxt . '",' . PHP_EOL .
                        '   "height": ' . $obj->height . ',' . PHP_EOL .
                        '   "heighttxt": "' . $obj->heighttxt . '"' . PHP_EOL .
                        '}';
                }
            }
            $db->free($resql);
        }
        
        $db->db->next_result(); // Stored procedure returns an extra result set :(
    }
}

$sql = "CALL get_estimate(".$id.")";
$resql=$db->query($sql);
if ($resql)
{
    if ($db->num_rows($resql))
    {
        if ($obj = $db->fetch_object($resql))
        {
            echo '{'.PHP_EOL.
                '  "id": ' . $obj->id . ','.PHP_EOL.
                '  "estimatenum": "' . cleanTxt($obj->estimatenum) . '",'.PHP_EOL.
                '  "quotedate": "' . cleanTxt($obj->quotedate) . '",'.PHP_EOL.
                '  "vendor": "' . cleanTxt($obj->vendor) . '",'.PHP_EOL.
                '  "vendor_phone": "' . cleanTxt($obj->vendor_phone) . '",'.PHP_EOL.
                '  "defcolor": "' . cleanTxt($obj->defcolor) . '",'.PHP_EOL.
                '  "defglasscolor": "' . cleanTxt($obj->defglasscolor) . '",'.PHP_EOL.
                '  "is_alteration": ' . cleanTxt($obj->is_alteration) . ','.PHP_EOL.
                '  "is_installation_included": ' . cleanTxt($obj->is_installation_included) . ','.PHP_EOL.
                '  "customerid": ' . cleanTxt($obj->customerid) . ','.PHP_EOL.
                '  "folio": "' . cleanTxt($obj->folio) . '",'.PHP_EOL.
                
                '  "deposit_percent": ' . cleanTxt($obj->deposit_percent) . ','.PHP_EOL.
                '  "deposit_percent_with_install": ' . cleanTxt($obj->deposit_percent_with_install) . ','.PHP_EOL.
                '  "percent_final_inspection": ' . cleanTxt($obj->percent_final_inspection) . ','.PHP_EOL.
                '  "warranty_years": ' . cleanTxt($obj->warranty_years) . ','.PHP_EOL.
                '  "pay_upon_completion": ' . cleanTxt($obj->pay_upon_completion) . ','.PHP_EOL.
                '  "new_construction_owner_responsability": ' . cleanTxt($obj->new_construction_owner_responsability) . ','.PHP_EOL.
                '  "status": "' . cleanTxt($obj->status) . '",'.PHP_EOL.
                '  "status_reason": "' . cleanTxt($obj->status_reason) . '",'.PHP_EOL.
                '  "approved_date": "' . cleanTxt($obj->approved_date) . '",'.PHP_EOL.
                '  "rejected_date": "' . cleanTxt($obj->rejected_date) . '",'.PHP_EOL.
                '  "delivered_date": "' . cleanTxt($obj->delivered_date) . '",'.PHP_EOL.
                (isset($obj->permitId) ? '  "permitId": ' . cleanTxt($obj->permitId) . ','.PHP_EOL : '').

                '  "add_sales_discount": ' . cleanTxt($obj->add_sales_discount) . ','.PHP_EOL.
                '  "add_inst_discount": ' . cleanTxt($obj->add_inst_discount) . ','.PHP_EOL.
                '  "permits": ' . cleanTxt($obj->permits) . ','.PHP_EOL.
                '  "salestax": ' . cleanTxt($obj->salestax) . ','.PHP_EOL.
                '  "totalprice": ' . cleanTxt($obj->totalprice) . ','.PHP_EOL.
                '  "notes": "' . cleanQuotes($obj->notes) . '",'.PHP_EOL.
                '  "public_notes": "' . cleanQuotes($obj->public_notes) . '",'.PHP_EOL.
                '  "qualifiername": "' . cleanQuotes($obj->qualifiername) . '",'.PHP_EOL.

                '  "customer": {'.PHP_EOL.
                '    "id": "' . cleanTxt($obj->customerid) . '",'.PHP_EOL.
                '    "customername": "' . cleanTxt($obj->customername) . '",'.PHP_EOL.
                '    "contactname": "' . cleanTxt($obj->contactname) . '",'.PHP_EOL.
                '    "contactphone": "' . cleanTxt($obj->contactphone) . '",'.PHP_EOL.
                '    "contactmobile": "' . cleanTxt($obj->contactmobile) . '",'.PHP_EOL.
                '    "contactaddress": "' . cleanTxt($obj->contactaddress) . '",'.PHP_EOL.
                '    "customeraddress": "' . cleanTxt($obj->customeraddress) . '",'.PHP_EOL.
                '    "customerzip": "' . cleanTxt($obj->customerzip) . '",'.PHP_EOL.
                '    "customercity": "' . cleanTxt($obj->customercity) . '",'.PHP_EOL.
                '    "customerstate": "' . cleanTxt($obj->customerstate) . '",'.PHP_EOL.
                '    "customerphone": "' . cleanTxt($obj->customerphone) . '",'.PHP_EOL.
                '    "customermobile": "' . cleanTxt($obj->customermobile) . '",'.PHP_EOL.
                '    "customeremail": "' . cleanTxt($obj->customeremail) . '",'.PHP_EOL.
                '    "folionumber": "' . cleanTxt($obj->folionumber) . '",'.PHP_EOL.
                '    "reltype": "' . cleanTxt($obj->reltype) . '"'.PHP_EOL.
                '  },'.PHP_EOL.
                '  "items": ['.PHP_EOL;
                        $isFirst=true;
                        foreach ($itemsJSON as $json) {
                            if (!$isFirst)
                                echo ','.PHP_EOL;
                            echo $json;
                            $isFirst = false;
                        }
                        echo PHP_EOL;
            echo '  ]'.PHP_EOL.
                '}';
            $db->free($resql);
            $db->db->next_result(); // Stored procedure returns an extra result set :(
            return 1;
        }
    }
    $db->free($resql);
}

$db->db->next_result(); // Stored procedure returns an extra result set :(

echo '{ "msg": "get_estimate(): Failed: ' . $db->lasterror() . '" }';
