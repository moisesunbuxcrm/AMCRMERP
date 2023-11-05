<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\autofill_location.js.php 

Automatically sets the location of an event based on the "Related Company" 

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var onRelatedCompanyChanged = function()
	{
		var socid = $("#socid").val();
		if (socid)
			$.ajax({
				url: "<?php echo DOL_URL_ROOT; ?>/custom/amhp/customers/search.php?id="+$("#socid").val(),
				dataType: 'json'
			}).then(function(data) {
				if (data.length == 0)
					return;

				var location = "";
				if (data[0]["address"])
					location += data[0]["address"];
				if (data[0]["town"])
				{
					if (location)
						location += ", ";
					location += data[0]["town"];
				}
				if (data[0]["state_code"])
				{
					if (location)
						location += ", ";
					location += data[0]["state_code"];
				}
				if (data[0]["zip"])
				{
					if (location)
						location += ", ";
					location += data[0]["zip"];
				}
				$("input[name='location']").val(location);
			});
	}

	$("input[name='location']").attr('class', 'minwidth300');
	$("#socid").change(onRelatedCompanyChanged);
	onRelatedCompanyChanged();
});
</script>
