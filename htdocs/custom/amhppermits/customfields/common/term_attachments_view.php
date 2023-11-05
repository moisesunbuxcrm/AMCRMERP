<?php
    // Call first include("term_attachments_options.php");

    foreach($options as $optkey => $optvalue) {
        if (strpos(",".$value.",", ",".$optvalue.",") !== false)
            print $optvalue."<br/>";
    }
?>
