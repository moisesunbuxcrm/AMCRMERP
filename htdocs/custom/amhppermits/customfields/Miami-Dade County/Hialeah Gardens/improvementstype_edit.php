<?php
    include("improvementstype_options.php");
    $otherFieldName="improvementstype_other";
    $otherFieldValue=$object->$otherFieldName;
    showCheckboxes($key, $value, $options, 2, true, -1, $otherFieldName, $otherFieldValue);
?>
