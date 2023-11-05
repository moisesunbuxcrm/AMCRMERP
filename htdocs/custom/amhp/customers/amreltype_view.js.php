<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\amreltype_view.js.php 

In create or edit customer form, move the mobile/secondary phone just below the primary phone

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var tptype_row = $("div.tabBar table.border tr").has("td:contains(Third-party type)").first();
	var reltype_row = $("div.tabBar table.border tr").has("table tr td:contains(Relationship Type)").first();
	tptype_row.after(reltype_row);
});
</script>
