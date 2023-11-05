<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\fix_create_event_link.js.php 

When in the Customer Card view, adjust the Create Event button to auto select the current customer

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var link = $("td>a:contains(Create event)")[0];
	if (link && link.href)
		link.href += "&socid=<?php echo $object->id ?>" ;
});
</script>

