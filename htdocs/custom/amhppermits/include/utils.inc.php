<?php
function showCheckboxes($key, $value, $options, $maxcols=2, $ckKeyIsValue=false, $maxSelections=-1, $otherFieldName="", $otherFieldValue="")
{
    print '<script>
    function '.$key.'Click(e) {
        maxSelections='.$maxSelections.';
        if (maxSelections >= 0 && $("#'.$key.'Table input:checked").length > maxSelections) {
            e.preventDefault();
            alert("You may only select "+maxSelections+" options");
        }
    }</script>';
    print '<table id="'.$key.'Table" style="border: 1px solid black; border-collapse: collapse; cell-padding: 20px;">';
    $currentcol = 0;
    $currentoption = 1;
    foreach($options as $optkey => $optvalue) {
        $cbKey = (string)$optkey;
        $cbValue = $optvalue;

        if ($ckKeyIsValue)
            $cbKey = $cbValue;

        if ($currentcol == 0)
            print '<tr>';

        $checked = "";
        if (strpos(",".$value.",", ",".$cbKey.",") !== false)
            $checked = "checked";

        if ($cbValue == "")
            print '<td></td><td></td>';
        else
            print '<td style="padding-left: 1em;">
                        <input onclick="'.$key.'Click(event)" id="'.$key.'opt'.$currentoption.'" type="checkbox" name="'.$key.'[]" value="'.$cbKey.'" '.$checked.'/>
                    </td>
                    <td>
                        <label for="'.$key.'opt'.$currentoption.'">'.$cbValue.'</label>
                    </td>';
        
        $currentcol++;
        $currentoption++;
        
        if ($currentcol==$maxcols) {
            print '</tr>';
            $currentcol = 0;
        }
    }

    if ($otherFieldName) {
        if ($currentcol > 0) {
            while ($currentcol<$maxcols) {
                print '<td></td><td></td>';
                $currentcol++;
            }
            $currentcol = 0;
            print '</tr>';
        }

        $opt = "Other";
        $checked = "";
        if (strpos(",".$value.",", ",".$opt.",") !== false)
            $checked = "checked";

        print '<tr><td style="padding-left: 1em;">';
        print   '<input id="'.$key.'opt'.$currentoption.'" type="checkbox" name="'.$key.'[]" value="'.$opt.'" '.$checked.'/>';
        print '</td><td colspan="'.($maxcols*2-1).'">';
        print   '<label for="'.$key.'opt'.$currentoption.'">'.$opt.'</label>';
        print   '<input onclick="'.$key.'ClickOther();" style="margin-left: 1em;" class="flat minwidth400 maxwidthonsmartphone" type="text" name="'.$key.'_other" value="'.$otherFieldValue.'" '.$checked.'/>';
        print '</td></tr>';

        print '<script>
        function '.$key.'ClickOther(e) {
            $("#'.$key.'opt'.$currentoption.'").prop("checked", true);
        }</script>';
    }

    if ($currentcol > 0) {
        while ($currentcol<$maxcols) {
            print '<td></td><td></td>';
            $currentcol++;
        }
        print '</tr>';
    }
    print '</table>';
}

function showRadioButtons($key, $value, $options, $maxcols=2, $otherFieldName="", $otherFieldValue="")
{
    print '<table style="border: 1px solid black; border-collapse: collapse;">';
    $currentcol = 0;
    $currentoption = 1;
    foreach($options as $opt) {
        if ($currentcol == 0)
            print '<tr>';

        if ($opt == "")
            print '<td></td><td></td>';
        else
        {
            $checked = "";
            if (strpos($value, $opt) !== false)
                $checked = "checked";
            print '<td style="padding-left: 1em;">';
            print   '<input id="'.$key.'opt'.$currentoption.'" type="radio" name="'.$key.'[]" value="'.$opt.'" '.$checked.'/>';
            print '</td><td>';
            print   '<label for="'.$key.'opt'.$currentoption.'">'.$opt.'</label>';
            print '</td>';
        }
        
        $currentcol++;
        $currentoption++;
        
        if ($currentcol==$maxcols) {
            print '</tr>';
            $currentcol = 0;
        }
    }

    if ($otherFieldName) {
        if ($currentcol > 0) {
            while ($currentcol<$maxcols) {
                print '<td></td><td></td>';
                $currentcol++;
            }
            $currentcol = 0;
            print '</tr>';
        }

        $opt = "Other";
        $checked = "";
        if (strpos($value, $opt) !== false)
            $checked = "checked";

        print '<tr><td style="padding-left: 1em;">';
        print   '<input id="'.$key.'opt'.$currentoption.'" type="radio" name="'.$key.'[]" value="'.$opt.'" '.$checked.'/>';
        print '</td><td colspan="'.($maxcols*2-1).'">';
        print   '<label for="'.$key.'opt'.$currentoption.'">'.$opt.'</label>';
        print   '<input onclick="'.$key.'ClickOther();" style="margin-left: 1em;" class="flat minwidth400 maxwidthonsmartphone" type="text" name="'.$key.'_other" value="'.$otherFieldValue.'" '.$checked.'/>';
        print '</td></tr>';

        print '<script>
        function '.$key.'ClickOther(e) {
            $("#'.$key.'opt'.$currentoption.'").prop("checked", true);
        }</script>';
    }

    if ($currentcol > 0) {
        while ($currentcol<$maxcols) {
            print '<td></td><td></td>';
            $currentcol++;
        }
        print '</tr>';
    }
    print '</table>';
}
?>