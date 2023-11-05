<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\autofill_enddate.js.php 

Automatically adjusts the start time and end time of an event when the user makes changes 

Only update start date if:
1. New event and Initial load and start date is empty

Only update end date if:
1. New event and Initial load and end date is empty
2. User changes start date

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var onStartDateChanged = function()
	{
		checkDates(true, false);
	}
	
	var checkDates = function(userChangedStartDate, initialLoad)
	{
		// Get Current Date
		var now = new Date();
		var nowyear = now.getFullYear();
		var nowmonth = now.getMonth();
		var nowday = now.getDate();
		var nowhour = now.getHours();
		var nowmin = now.getMinutes();
		
		// Validate Start Date and Time
		var apyear = $("#apyear").val();
		var apmonth = $("#apmonth").val();
		if (apmonth != "")
			apmonth = ensureDoubleDigits(parseInt(apmonth));
		var apday = $("#apday").val();
		if (apday != "")
			apday = ensureDoubleDigits(parseInt(apday));
		var aphour = $("#aphour").val();
		if (aphour != "")
			aphour = ensureDoubleDigits(parseInt(aphour));
		var apmin = $("#apmin").val();
		if (apmin != "")
			apmin = ensureDoubleDigits(parseInt(apmin));
		
		if (apyear == "") apyear = nowyear.toString();
		if (apmonth == "") apmonth = ensureDoubleDigits(nowmonth+1); // Note: month is 0-11
		if (apday == "") apday = ensureDoubleDigits(nowday);
		if (aphour =="-1") aphour = ensureDoubleDigits(nowhour+1);
		if (apmin == "-1") apmin = ensureDoubleDigits(0);
		
		var apdate = apmonth + "/" + apday + "/" + apyear;
		startDateChanged = userChangedStartDate;
		if (initialLoad && isNewEvent() && isStartDateEmpty())
		{
			startDateChanged |= updateElement("ap", apdate);
			startDateChanged |= updateElement("apyear", apyear);
			startDateChanged |= updateElement("apmonth", apmonth);
			startDateChanged |= updateElement("apday", apday);
			startDateChanged |= updateElement("aphour", aphour);
			startDateChanged |= updateElement("apmin", apmin);
		}

		if (startDateChanged || (initialLoad && isNewEvent() && isEndDateEmpty()))
		{
			// Update End Date and Time to one hour after Start
			var enddatetime = new Date(parseInt(apyear), parseInt(apmonth)-1, parseInt(apday), parseInt(aphour)+1, parseInt(apmin), 0, 0);
			var p2year = enddatetime.getFullYear().toString();
			var p2month = ensureDoubleDigits(enddatetime.getMonth()+1);
			var p2day = ensureDoubleDigits(enddatetime.getDate());
			var p2hour = ensureDoubleDigits(enddatetime.getHours());
			var p2min = ensureDoubleDigits(enddatetime.getMinutes());
			
			var p2date = p2month + "/" + p2day + "/" + p2year;
			updateElement("p2", p2date);
			updateElement("p2year", p2year);
			updateElement("p2month", p2month);
			updateElement("p2day", p2day);
			updateElement("p2hour", p2hour);
			updateElement("p2min", p2min);
		}
	}

	var isNewEvent = function()
	{
		return window.location.href.indexOf("action=create") > 0;
	}

	var isStartDateEmpty = function()
	{
		return $("#apyear").val() == "";
	}

	var isEndDateEmpty = function()
	{
		return $("#p2year").val() == "";
	}

	var ensureDoubleDigits = function(n)
	{
		if (n<10 && n>=0)
			return "0"+n.toString();
		return n.toString();
	}
	
	var updateElement = function(id, val)
	{
		if ($("#"+id).val() != val)
		{
			$("#"+id).val(val);
			return true;
		}
		return false;
	}

	$("#apday").change(onStartDateChanged);
	$("#apmonth").change(onStartDateChanged);
	$("#apyear").change(onStartDateChanged);
	$("#aphour").change(onStartDateChanged);
	$("#apmin").change(onStartDateChanged);
	$("#ap").change(onStartDateChanged); // Catches manual edits to start date
	$("#apButtonNow").click(onStartDateChanged);
	
	var old_dpChangeDay = dpChangeDay;
	dpChangeDay = function(id, format){
		old_dpChangeDay(id, format);
		if (id=='ap')
			onStartDateChanged();
	}
	
	checkDates(false, true);
});
</script>
