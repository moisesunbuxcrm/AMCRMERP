<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\add-working-day-radio-button.js.php 

Adds a button/link to the event when editing it to automatically set the start and end time of the event to the full working day.
-->
<script type="text/javascript">
$(document).ready(function () 
{
	var nowButton = $("#apButtonNow");
	var template_text = $("#ea_working_day_button_template").html();
		
	// Inject template after the now button
	var ea_working_day_button_template = $($.parseHTML(template_text));
	$(nowButton).after(ea_working_day_button_template[1]); 

	if (!window.ea)
		window.ea = {};
	window.ea.setFullWorkingDay = function(event, owner)
	{
		$('#fullday').prop('checked', false).change();
		$('#aphour').val('08'); 
		$('#apmin').val('00'); 
		$('#p2hour').val('18');
		$('#p2min').val('30');
	};
});
</script>

<script id="ea_working_day_button_template" type="text/x-custom-template">
	<button class="dpInvisibleButtons datenowlink" id="apButtonWorkingDay" 
		style="margin-left: 20px"
		type="button" name="_useless" value="Full Working Day" 
		onclick="window.ea.setFullWorkingDay()">Full Working Day</button>
</script>
