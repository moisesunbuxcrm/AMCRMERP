<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\add-change-owner-buttons.js.php 

Addes buttons to "Event assigned to" section that allow you to change the owner of an event. Only
appears when editing an event.
-->
<script type="text/javascript">
$(document).ready(function () 
{
	var event_id = <?php print $object->id==""?-1:$object->id; ?>;
	
	var delete_assigned_buttons = $(".attendees input.removedassigned");
	delete_assigned_buttons = delete_assigned_buttons.slice(1); // Remove first user (owner)
	delete_assigned_buttons.each(function(index, button) {
		var template_text = $("#ea_change_owner_button_template").html();
		
		// Replace tokens in template...
		template_text = template_text.replace("$event_id", event_id);
		template_text = template_text.replace("$owner", $(button).attr("value"));
		
		// Inject template after the delete button
		var change_owner_button_template = $($.parseHTML(template_text));
		$(button).after(change_owner_button_template[1]); 
	});

	if (!window.ea)
		window.ea = {};
	window.ea.changeOwner = function(event, owner)
	{
		$.ajax({
			url: "<?php echo DOL_URL_ROOT; ?>/custom/amhp/agenda/change-owner.php?e="+event+"&u="+owner,
			dataType: 'html'
		}).then(function(data) {
			//alert(data); // New order of assigned users
			$("form[name='formaction']").submit();
		});
	};
});
</script>

<script id="ea_change_owner_button_template" type="text/x-custom-template">
    <img 
		style="border: 0px;margin-bottom: -2px;cursor:pointer;" 
		src="<?php echo DOL_URL_ROOT; ?>/theme/eldy/img/external.png" 
		alt="Make new owner" 
		class="inline-block valigntextbottom"
		onclick="ea.changeOwner($event_id, $owner);"/>
</script>
