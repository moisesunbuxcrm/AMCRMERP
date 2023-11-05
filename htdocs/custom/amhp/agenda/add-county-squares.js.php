<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\add-county-squares.js.php 

Goes through all the events in the calendar monthly view and determines the county that each event belongs to.
Based on the county we add a coloured square to the event information:

Miami-Dade -> blue
Broward -> red
Palm Beach -> yellow
Monroe -> brown
Everything else -> green

-->
<?php
require_once '../../main.inc.php';

global $eventarray;

print '<script type="text/javascript">'."\n";
print '	if (!window.ea)'."\n";
print '		window.ea = {};'."\n";
print '	window.ea.eventmap = '."\n";
print '	{'."\n";
	
foreach ($eventarray as $daykey => $notused)
{
	foreach ($eventarray[$daykey] as $index => $event)
	{
		print '		';
		print_county_square($event);
		print "\n";
	}
}

print '	}'."\n";

print '</script>'."\n";

/**
 * Inserts square inside event that is colored depending on the county pdermody
 */
function print_county_square($event)
{
	global $db, $cachethirdparties;
	
	$socid = $event->thirdparty_id;
	if (!$socid)
		$socid = $event->socid;
    if (! isset($cachethirdparties[$socid]) || ! is_object($cachethirdparties[$socid]))
    {
        $thirdparty=new Societe($db);
        $thirdparty->fetch($socid);
        $cachethirdparties[$socid]=$thirdparty;
    }
    else $thirdparty=$cachethirdparties[$socid];

    $county = "unknown";
    $color = "green";

	$sql = "SELECT s.county";
	$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
	$sql.= " WHERE s.town = '".$thirdparty->town."'";
	$sql.= " AND s.status = 1";

	$resql=$db->query($sql);
	if ($resql)
	{
		if ($row = $db->fetch_object($resql))
		{
			if ($row->county)
				$county = $row->county;
		}
		$db->free($resql);
	}

    switch ($county) {
        case "Miami-Dade":
            $color="blue";
            break;
        case "Broward":
            $color="red";
            break;
        case "Palm Beach":
            $color="yellow";
            break;
        case "Monroe":
            $color="brown";
            break;
    }

    print $event->id.':{county:"'.$county.'",color:"'.$color.'"},';
}
?>

<script type="text/javascript">
$(document).ready(function () 
{
	var template_text = $("#ea_county_square_template").html();
	var eventLinks = $(".cal_event");
	eventLinks.each(function() {
		if ($(this)[0].nodeName == "A")
		{
			var href = $( this ).attr( "href" );
			var eventIdMatches=/id=(.*)/.exec(href);
			var eventId = null;
			if (eventIdMatches && eventIdMatches.length>1)
				eventId=eventIdMatches[1];
    		if (eventId !== undefined && eventId.length>0)
			{
				var rightTd = $(this)[0].parentNode.nextSibling;

				var new_template_text = template_text.replace("$county", window.ea.eventmap[eventId].county);
				new_template_text = new_template_text.replace("$color", window.ea.eventmap[eventId].color);

				var ea_county_square_template = $($.parseHTML(new_template_text));
				$(rightTd).append(ea_county_square_template[1]); 
			}
		}
  	});
});
</script>

<script id="ea_county_square_template" type="text/x-custom-template">
	<div title="$county" style="width:16px; height:16px; background-color:$color;"/>
</script>
