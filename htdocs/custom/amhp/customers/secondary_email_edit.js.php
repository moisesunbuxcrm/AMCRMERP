<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\secondary_email_edit.js.php 

In create or edit customer form, move the secondary email up beside the primary email

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var semail_row = $("tr.societe_extras_secondemail");
	var email_row = $("tr").has("input#email");
	email_row.after(semail_row);
});
</script>
