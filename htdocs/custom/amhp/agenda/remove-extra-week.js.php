<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\remove-extra-week.js.php 

In the Month view, do not display events from the previous or following months even if there are days visible from those months.

-->
<?php
require_once '../../main.inc.php';

global $day,$month,$year,$mode,$eventarray;
if (empty($mode) || $mode=='show_month') 
{
	foreach ($eventarray as $daykey => $eventstoday)
	{
		$newarray = array();
		foreach($eventstoday as $event)
		{
			// Check if event month starts or ends in $month
			if ($month == date('m',$event->datep) || $month == date('m',$event->datef))
			{
				array_push($newarray,$event);
			}
		}
		$eventarray[$daykey] = $newarray;
	}
}
?>
