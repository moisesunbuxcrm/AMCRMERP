<?php
    include("improvementstype_options.php");
    $otherFieldName="improvementstype_other";
    $otherFieldValue=$object->$otherFieldName;
    showRadioButtons($key, $value, $options, 3, $otherFieldName, $otherFieldValue);
?>
