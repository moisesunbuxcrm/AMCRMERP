<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\mobile_phone_edit.js.php 

In create or edit customer form, move the mobile/secondary phone just below the primary phone

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var mphone_row = $("tr.societe_extras_mobilephone");
	var phone_row = $("tr").has("input#phone");
	phone_row.after(mphone_row);
});
</script>
