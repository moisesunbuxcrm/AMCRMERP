<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\hide_vat_view.js.php 

Hide VAT fields

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$("tr").has("td:contains(VAT is used)").hide();
	$("tr").has("td:contains(VAT number)").hide();
	
});
</script>
