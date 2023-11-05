<!-- Customization by pdermody@elementalley.com from custom module amhp\products\default_units.js.php 

Automatically set the Construction Type on the new product screen to Alteration

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$('[name="options_amconsttype"]').removeAttr('checked');
	$("input[name=options_amconsttype][value=1]").prop('checked', true);
});
</script>
