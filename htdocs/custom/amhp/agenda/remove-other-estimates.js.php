<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\remove-other-estimates.js.php 

Do not show estimates from other salespeople unless you are admin. Other types of events behave normally.

-->
<?php
require_once '../../main.inc.php';

global $user,$eventarray;

// Admins can see all events
if (false)//!$user->admin) 
{
	$neweventarray = array();
	foreach ($eventarray as $daykey => $eventstoday)
	{
		$neweventstoday = array();
		foreach($eventstoday as $event)
		{
			if ($event->type_code != "AC_ESTIMATE" || array_key_exists($user->id, $event->userassigned))
			{
				array_push($neweventstoday,$event);
			}
		}
		if (count($neweventstoday))
		{
			$neweventarray[$daykey] = $neweventstoday;
		}
	}
	
	$eventarray = $neweventarray;
}
?>
