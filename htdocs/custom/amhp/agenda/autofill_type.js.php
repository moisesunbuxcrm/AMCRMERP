<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\autofill_type.js.php 

Check that we automatically set the type of the event based on the page from which the event was
created. For example, if we are viewing “Installations” then any new event will automatically be 
an installation event.

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var match=/actioncode=(.*?)&/.exec(document.referrer);
	if (match)
	{
		var actioncode=/actioncode=(.*?)&/.exec(document.referrer)[1];
		if (actioncode !== undefined && actioncode.length>0)
			$("#actioncode").val(actioncode).change();
	}
});
</script>
