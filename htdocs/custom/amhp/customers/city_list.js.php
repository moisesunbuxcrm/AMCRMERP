<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\city_list.js.php 

Add button to switch City between a text field and a dropdown

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var city_list_row = $("tr#ea_city_list_row_template"); // Row with new cell with a City dropdwon
	var town_row = $("tr").has("input#town");
	town_row.append(city_list_row.contents()); // Move new city dropdown into place 
	
	var ea_city_list_cell = $("td#ea_city_list_cell"); // New cell with a City dropdwon
	var town_cell = $("td").has("input#town");
	var switch_to_dropdown = $("div#ea_switch_to_dropdown_template");
	town_cell.append(switch_to_dropdown.contents());
	
	var supplier_dropdown = $("#fournisseur");
	var thirdparty_type_dropdown = $("#typent_id");
	
	var town_input = $("input#town");
	var town2_input = $("select#town2");
	town_input.val(town_input.val().toUpperCase()); // Force to upper case

	var show_city_dropdown = function()
	{
		town_cell.hide();
		ea_city_list_cell.show();
	};
	
	var hide_city_dropdown = function()
	{
		town_cell.show();
		ea_city_list_cell.hide();
	};
	
	var show_hide_city_dropdown = function()
	{
		if (supplier_dropdown.val() == 1 && thirdparty_type_dropdown.val() == 5)
		{
			hide_city_dropdown();
			$("span.ea_town_switcher").hide();
		}
		else
		{
			show_city_dropdown();
			$("span.ea_town_switcher").show();
		}
	};

	var toggle_city_dropdown = function()
	{
		if (ea_city_list_cell.is(":visible"))
			hide_city_dropdown();
		else
			show_city_dropdown();
	};

	var update_folio = function()
	{
		var builddept = $('input[name="options_buildingdeptcity"]').val();
		var expectedfolios = window.folios
				.filter(f => builddept === f.builddept);
		var currentfolio = $("input#barcode").val().trim().substring(0,2);
		var expectedfolio = null;

		if (expectedfolios.length > 0)
			expectedfolio = expectedfolios[0];
		if (expectedfolio && expectedfolio.folio_prefix && currentfolio !== expectedfolio.folio_prefix)
			$("input#barcode").val(expectedfolio.folio_prefix+"-");
	}	

	// Update town2 to null and hide dropdown
	var update_town2 = function()
	{
		var s1 = town2_input.val();
		if (s1 == "" || s1 == "-1") // Catch empty dropdown
			s1 = null;
		
		if (s1 != null)
		{	
			town2_input.val(-1).change();
		}
		hide_city_dropdown();
	}
	
	// Update town with value from town2
	var update_town = function()
	{
		var separator = " / ";
		var s1 = town2_input.val();
		if (s1 !== null)
			s1 = s1.toUpperCase();
		if (s1 == "" || s1 == "-1") // Catch empty dropdown
			s1 = null;
		if (s1 != null)
			s1 = s1.split(separator)[0];
		
		var s2 = town_input.val().toUpperCase();
		if (s2 == "")
			s2 = null;
		
		if (s1 != null)
		{
			if (s1 != s2)
			{
				// Update Building Department City field
				var buildingdeptcity = town2_input.val().toUpperCase();
				buildingdeptcity = buildingdeptcity.split(separator).pop();
				$('input[name="options_buildingdeptcity"]').val(buildingdeptcity).change();
			}
			town_input.val(s1).change();
		}
		update_folio();
	}
	
	hide_city_dropdown();
	$("span.ea_town_switcher").click(toggle_city_dropdown);
	
	// Keep original town input control and new town2 dropdown in sync
	town_input.change(update_town2);
	town2_input.change(update_town);

	// Move County field to top
	var county_row = $("tr.societe_extras_amcounty");
	var target_row = $("table.border tr").has("input#zipcode");
	target_row.before(county_row);

	var updateCityList = () => {
		let county = $("#options_amcounty").val().trim().toUpperCase();
		$("#options_amcounty").val(county);
		let folio = $("input#barcode").val().trim().substring(0,2);

		// Only filter cities Miami Dade and Palm Beach, and only if there is a folio number
		let currentTown = town_input.val();
		let currentTownValid = false;
		let filterCities = 
			((county === "MIAMI DADE") || (county === "PALM BEACH"))
			&& folio !== "";
		filterCities = filterCities || county === "BROWARD"; 
		
		let getNameFromFolio = f => {
			if (f.city && f.city === currentTown)
				currentTownValid = true;
			let name = f.city;
			if (name && f.builddept)
				name += ' / ';
			else
				name = "";
			if (f.builddept)
				name += f.builddept;
			name = name.toUpperCase();
			return {
				id: name,
				text: name
			};
		};

		let filteredCities = window.folios
			.filter(f => !filterCities || (f.county?.toUpperCase()===county && (folio === "" || f.folio_prefix === folio)))
			.map(getNameFromFolio);
		if (filteredCities.length === 0) // If no city matches the current options then show all of them
			filteredCities = window.folios.map(getNameFromFolio);

		town2_input.select2().empty();
		town2_input.select2({
			data: filteredCities
		});

		if (currentTownValid)
			town_input.val(currentTown).change();
		else
			town_input.val("").change();

		if (filteredCities.length === 1)
			town2_input.val(filteredCities[0].id).change()
	}
	updateCityList();

	// Handle changes to county and barcode/folio
	$("#options_amcounty").change(updateCityList);
	$("input#barcode").change(updateCityList);

});
</script>

<?php
require_once '../main.inc.php';
$form = new Form($db);
$formcompany = new FormCompany($db);

function city_array()
{
	global $db;
	$cities = array();

	$sql = "SELECT DISTINCT s.town, s.city_name as city ";
	$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
	$sql.= " WHERE s.town is not null";                //Moved back to town 01210218 " WHERE s.city_name is not null";  by GAcosta
	$sql.= " AND s.status = 1";
	$sql.= " ORDER by s.town";
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($row = $db->fetch_object($resql))
		{
			if ($row->city != "")
			//	$cities[$row->town] = $row->town . " / " . $row->city;
				array_push($cities, strtoupper($row->town . " / " . $row->city));

		}
		$db->free($resql);
	}

	return $cities;
}

function folio_array()
{
	global $db;
	$folios = array();

	$sql = "SELECT DISTINCT id, county, folio_prefix, city, builddept ";
	$sql.= " FROM ".MAIN_DB_PREFIX."ea_foliocities";
	$sql.= " ORDER by coalesce(concat(city , '/' , builddept), city, builddept)";
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($row = $db->fetch_object($resql))
		{
			array_push($folios,$row);
		}
		$db->free($resql);
	}

	return $folios;
}

print '<script type="text/javascript">';
print 'window.folios = '.json_encode(folio_array()) . ';';
print '</script>';

print '<table style="display:none"><tr id="ea_city_list_row_template">';
	print '<td id="ea_city_list_cell" class="maxwidthonsmartphone">'."\n";
	print $form->selectarray("town2", city_array(), strtoupper($object->town), 1, 0, 1, 'style="width: 95%"', 0, 0, 0, '', 'minwidth200', 1);
	print ' <span class="ea_town_switcher">'.img_edit('Click here to type a new value').'</span>';
	print '</td>';
print '</tr></table>';
print '<div id="ea_switch_to_dropdown_template" style="display:none">';
	print ' <span class="ea_town_switcher">'.img_edit('Click here to select new value from a list').'</span>';
print '</div>';
?>
