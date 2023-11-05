<?php
    include("permittype_options.php");
    $otherFieldName="permittype_other";
    $otherFieldValue=$object->$otherFieldName;
    showRadioButtons($key, $value, $options, 1, $otherFieldName, $otherFieldValue);
?>
