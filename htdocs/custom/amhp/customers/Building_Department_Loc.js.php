<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\Building_Department_Loc.js.php -->
<script type="text/javascript">
$(document).ready(function () 
{
	var buildingdeptcity_row = $("tr.societe_extras_buildingdeptcity");
	var stateid_row = $("tr").has("#state_id");
	stateid_row.after(buildingdeptcity_row);
});
</script>
