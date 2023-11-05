<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\hide_vat_edit.js.php 

Hide the VAT fields

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var vatused_row = $("tr").has("select#assujtva_value");
	vatused_row.hide();
	
	var vatnujm_row = $("tr").has("input#intra_vat");
	vatnujm_row.hide();
});
</script>
