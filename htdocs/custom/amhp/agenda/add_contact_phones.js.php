<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\add_contact_phones.js.php 

Adds contact info to the event card View for any existing “Related company”

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var location_row = $("div.tabBar table.border tr").has("td:contains(Location)").first(); // Row with location field
	var phone_row = $("table#ea_location_row_template tbody"); // Row with new phone number template
	phone_row.contents().insertAfter(location_row);
});
</script>


<?php
require_once '../../main.inc.php';

function getPhoneInfo($socid)
{
	global $db;
	
	$sql = "CALL getPOInitialData(".$socid.", '')";
	$resql=$db->query($sql);
	if ($resql)
	{
	  if ($db->num_rows($resql))
	  {
		$obj = $db->fetch_object($resql);
	  }
	  $db->free($resql);
	  $db->db->next_result(); // Stored procedure returns an extra result set :(
	  return $obj;
	}
	return null;
}

if (getPhoneInfo($object->thirdparty->id))
	$tp = getPhoneInfo($object->thirdparty->id);

if ($tp)
{
	print '<table id="ea_location_row_template" style="display:none">';
	print '<tr><td>Phone 1</td><td colspan="3">'.$tp->PHONENUMBER1.'</td></tr>';
	print '<tr><td>Phone 2</td><td colspan="3">'.$tp->PHONENUMBER2.'</td></tr>';
	print '<tr><td>Fax</td><td colspan="3">'.$tp->FAXNUMBER.'</td></tr>';
	print '<tr><td>Contact Name</td><td colspan="3">'.$tp->CONTACTNAME.'</td></tr>';
	print '<tr><td>Contact Phone 1</td><td colspan="3">'.$tp->CONTACTPHONE1.'</td></tr>';
	print '<tr><td>Contact Phone 2</td><td colspan="3">'.$tp->CONTACTPHONE2.'</td></tr>';
	print '</tr></table>';
}
?>