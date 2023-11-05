<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\amreltype_edit.js.php 

In create or edit customer form, move the mobile/secondary phone just below the primary phone

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var tptype_row = $("tr.societe_extras_amreltype");
	var reltype_row = $("tr").has("#typent_id");
	reltype_row.after(tptype_row);
});
</script>
