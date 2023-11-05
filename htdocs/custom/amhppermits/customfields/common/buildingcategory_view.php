<?php
    // Call first include("buildingcategory_options.php");

    foreach($options as $optkey => $optvalue) {
        if (strpos(",".$value.",", ",".$optkey.",") !== false)
            print $optvalue."<br/>";
    }
?>
