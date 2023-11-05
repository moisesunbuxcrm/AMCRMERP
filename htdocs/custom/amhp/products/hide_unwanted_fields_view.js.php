<!-- Customization by pdermody@elementalley.com from custom module amhp\products\hide_unwanted_fields_view.js.php -->
<script type="text/javascript">
$(document).ready(function () 
{
	$("tr").has("td:contains('Public URL')").hide();
	$("tr").has("td:contains('Weight')").hide();
	$("tr").has("td:contains('Length x Width x Height')").hide();
	$("tr").has("td:contains('Area')").hide();
	$("tr").has("td:contains('Volume')").hide();
	$("tr").has("td:contains('Customs/Commodity/HS code')").hide();
	$("tr").has("td:contains('Origin country')").hide();

	$("tr").has(".product_extras_amwidth").hide();
	$("tr").has(".product_extras_amheight").hide();
	$("tr").has(".product_extras_amlength").hide();
});
</script>
