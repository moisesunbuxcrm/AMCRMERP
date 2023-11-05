<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\builddept_city_list.js.php 

Add button to switch Building City Department between a text field and a dropdown
Add a button to display details of building department city
Hide these for governmental third party type

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var city_list_row = $("tr#ea_builddept_city_list_row_template"); // Row with new cell with a Building Department City dropdwon
	var builddeptcity_row = $("tr").has('input[name="options_buildingdeptcity"]');
	builddeptcity_row.append(city_list_row.contents()); // Move new Building Department City dropdown into place 
	
	var ea_builddept_city_list_cell = $("td#ea_builddept_city_list_cell"); // New cell with a Building Department City dropdwon
	var buildingdeptcity_cell = $("td").has('input[name="options_buildingdeptcity"]');
	var switch_to_dropdown = $("div#ea_switch_to_builddept_dropdown_template");
	buildingdeptcity_cell.append(switch_to_dropdown.contents());
	
	var supplier_dropdown = $("#fournisseur");
	var thirdparty_type_dropdown = $("#typent_id");
	
	var builddeptcity_input = $('input[name="options_buildingdeptcity"]');
	var builddeptcity2_input = $("select#builddeptcity2");

	var show_city_dropdown = function()
	{
		buildingdeptcity_cell.hide();
		ea_builddept_city_list_cell.show();
	};
	
	var hide_city_dropdown = function()
	{
		buildingdeptcity_cell.show();
		ea_builddept_city_list_cell.hide();
	};
	
	var show_hide_city_dropdown = function()
	{
		if (supplier_dropdown.val() == 1 && thirdparty_type_dropdown.val() == 5)
		{
			hide_city_dropdown();
			$("span.ea_builddeptcity_switcher").hide();
		}
		else
		{
			show_city_dropdown();
			$("span.ea_builddeptcity_switcher").show();
		}
	};

	var toggle_city_dropdown = function()
	{
		if (ea_builddept_city_list_cell.is(":visible"))
			hide_city_dropdown();
		else
			show_city_dropdown();
	};
	
	// Update builddeptcity2 with value from builddeptcity if it exists in the dropdown
	var update_builddeptcity2 = function()
	{
		var s1 = builddeptcity2_input.val();
		if (s1 !== null)
			s1 = s1.toUpperCase();
		if (s1 == "" || s1 == "-1") // Catch empty dropdown
			s1 = null;
		
		var s2 = builddeptcity_input.val().toUpperCase();
		if (s2 == "")
			s2 = null;
		
		if (s1 != s2)
		{	
			var newval = builddeptcity_input.val().toUpperCase();
			builddeptcity2_input.val(newval).change();
		}
	}

	// Update builddeptcity with value from builddeptcity2
	var update_builddeptcity = function()
	{
		var s1 = builddeptcity2_input.val();
		if (s1 !== null)
			s1 = s1.toUpperCase();
		if (s1 == "" || s1 == "-1") // Catch empty dropdown
			s1 = null;
		
		var s2 = builddeptcity_input.val().toUpperCase();
		if (s2 == "")
			s2 = null;
		
		if (s1 != null && s1 != s2)
			builddeptcity_input.val(builddeptcity2_input.val().toUpperCase()).change();
	}
	
	// Enable appropriate code depending on type of third party
	show_hide_city_dropdown();
	
	// Monitor value of Supplier and Third Pary Type fields
	supplier_dropdown.change(show_hide_city_dropdown);
	thirdparty_type_dropdown.change(show_hide_city_dropdown);
	$("span.ea_builddeptcity_switcher").click(toggle_city_dropdown);
	
	// Hide dropdown if current value is not in select
	if (builddeptcity_input.val() != "")
	{
		if (builddeptcity2_input.find("option[value='"+builddeptcity_input.val().toUpperCase()+"']").length>0)
		{
			builddeptcity2_input.val(builddeptcity_input.val().toUpperCase()).change();
		}
		else
		{
			hide_city_dropdown();
		}
	}

	// Keep original town input control and new town2 dropdown in sync
	builddeptcity_input.change(update_builddeptcity2);
	builddeptcity2_input.change(update_builddeptcity);
});

$(document).ready(function () 
{
	$( "span.ea_builddeptcity_popup_button" ).click(function() {		
		var builddeptcity2_input = $("select#builddeptcity2");
		if (builddeptcity2_input.val() != "")
			$.ajax({
				url: "<?php echo DOL_URL_ROOT; ?>/custom/amhp/builddepts/search.php?cityname="+builddeptcity2_input.val(),
				dataType: 'html'
			}).then(function(data) {
				if (data.length == 0)
					return;
				$("#ea_builddeptcity_popup").html(data);
				$("#ea_builddeptcity_popup").dialog({
					draggable: false,
					modal: false,
					classes: {
						"ui-dialog": "ui-corner-all"
					},
					position: { my: "right top", at: "right bottom", of: $( "span.ea_builddeptcity_popup_button" ), collision: "fit" },
					resizable: false,
					// open: function(event, ui) {
						// $(".ui-dialog-titlebar").hide();
					// },
					show: {
						effect: "fade",
						duration: 200
					},
					hide: {
						effect: "fade",
						duration: 200
					},
					title: "Building Department Details"
				});
			});
	}); 
});

</script>
<div id="ea_builddeptcity_popup"></div>


<?php
require_once '../main.inc.php';
$form = new Form($db);
$formcompany = new FormCompany($db);

function builddept_city_array()
{
	global $db;
	$cities = array();

	$sql = "SELECT DISTINCT s.town, s.city_name as city ";
	$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
	$sql.= " WHERE s.city_name is not null";
	$sql.= " AND s.status = 1";
	$sql.= " ORDER by city";
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($row = $db->fetch_object($resql))
		{
			if ($row->city != "")
			//	$cities[$row->city] = $row->town . " / " . $row->city;
				array_push($cities, strtoupper($row->city));

		}
		$db->free($resql);
	}

	return $cities;
}

print '<table style="display:none"><tr id="ea_builddept_city_list_row_template">';
	print '<td id="ea_builddept_city_list_cell" class="maxwidthonsmartphone">'."\n";
		print $form->selectarray("builddeptcity2", builddept_city_array(), strtoupper($object->builddeptcity), 1, 0, 1, '', 0, 0, 0, '', 'minwidth200', 1);
		print ' <span class="ea_builddeptcity_switcher">'.img_edit('Click here to type a new value').'</span>';
		print ' <span class="ea_builddeptcity_popup_button cursorpointer">'.img_view('Click here to view details about Building Department').'</span>';
	print '</td>';
print '</tr></table>';
print '<div id="ea_switch_to_builddept_dropdown_template" style="display:none">';
	print ' <span class="ea_builddeptcity_switcher">'.img_edit('Click here to select new value from a list').'</span>';
print '</div>';
?>