<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\phone-number-masks.js.php 

In the form to create or edit a new Customer, all phone numbers should require a valid phone number format

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$('#phone').mask('(000) 000-0000',{placeholder: "(___) ___-____"});
	$('#fax').mask('(000) 000-0000',{placeholder: "(___) ___-____"});
	$('input[name="options_mobilephone"]').mask('(000) 000-0000',{placeholder: "(___) ___-____"});
});
</script>
