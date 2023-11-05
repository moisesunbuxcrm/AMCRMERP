<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\add_builddept_url.js.php 

Add a building department URL to bottom of right column of Third Party card

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var builddept_url_row = $("table#ea_builddept_url_row_template"); // Row with building department url
	var righttable = $("div.fichehalfright table.tableforfield"); // Right hand table in customer card
	righttable.append(builddept_url_row.contents()); // Move new city dropdown into place 
});
</script>


<?php
require_once '../main.inc.php';

function url_for_city($town)
{
	global $db;
	
	$url = "";

	$sql = "SELECT s.url";
	$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
	$sql.= " WHERE s.town = '".$town."'";
	$sql.= " AND s.status = 1";

	$resql=$db->query($sql);
	if ($resql)
	{
		if ($row = $db->fetch_object($resql))
		{
			$url = $row->url;
		}
		$db->free($resql);
	}

	return $url;
}

$url = url_for_city($object->town);

print '<table  id="ea_builddept_url_row_template" style="display:none">'."\n";
print '  <tr>'."\n";
print '    <td>Building Department</td>'."\n";
print '    <td><a target="_blank" href="'.$url.'">'.$url.'</a></td>'."\n";
print '  </tr>'."\n";
print '</table>'."\n";
?>