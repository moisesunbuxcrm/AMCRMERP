<?php

class Actionsamhp
{ 
	/**
	 * Overloading the formObjectOptions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		// echo "<pre>";
		// echo "action="; print_r($action); echo "\n";
		// echo "parameters="; print_r($parameters); echo "\n";
		// echo "object="; print_r($object); echo "\n";
		// echo "</pre>";
		
		if (strpos($parameters['context'], ':thirdpartycard:'))
		{
			$this->includeThirdPartyCustomizations($parameters, $object, $action, $hookmanager);
		}
		else if (strpos($parameters['context'], ':productcard:'))
		{
			$this->includeProductCustomizations($parameters, $object, $action, $hookmanager);
		}
		else if (strpos($parameters['context'], ':actioncard:'))
		{
			$this->includeAgendaCardCustomizations($parameters, $object, $action, $hookmanager);
		}
		else if (strpos($parameters['context'], ':contactcard:'))
		{
			$this->includeContactCustomizations($parameters, $object, $action, $hookmanager);
		}
	}

	function printLeftBlock($parameters, &$object, &$action, $hookmanager) {
		$mainmenu = (empty($_SESSION["mainmenu"]) ? '' : $_SESSION["mainmenu"]);
		if ($mainmenu == "companies") 
		{
			include(__DIR__."/../customers/reorder_leftmenu.js.php");
		}

		/*
		   Always show a Search Customers button in the left menu to make it very fast for users to locate a customer by their phone number
		 */
		print '<div class="blockvmenu blockvmenupair">';
		print '<div class="menu_titre" style="text-align: center;"><a id="search_customers_link" href="" class="butAction">Search Customers</a></div>';
		print '<script type="text/javascript">
			$(document).ready(function () 
			{
				var searchlink = $("a#search_customers_link");
				searchlink.click(function(event) {
					event.preventDefault();
					var term = prompt("Please enter a phone number", "1234");
					if (term)
						window.location.href = "'.DOL_URL_ROOT.'/societe/list.php?search_customers_phones="+term;
				});
			});
		</script>';

		/*
		   Always show a Search Events button in the left menu to make it very fast for users to locate a customer by their phone number
		 */
		print '<div class="menu_titre" style="text-align: center;"><a id="search_events_link" href="" class="butAction">Search Events</a></div>';
		print '<script type="text/javascript">
			$(document).ready(function () 
			{
				var searchlink = $("a#search_events_link");
				searchlink.click(function(event) {
					event.preventDefault();
					var term = prompt("Please enter a phone number", "1234");
					if (term)
						window.location.href = "'.DOL_URL_ROOT.'/custom/amhp/agenda/list.php?sortfield=a.datep,a.id&sortorder=desc,desc&search_filtert=-1&custom_search_events="+term;
				});
			});
		</script>';
		print '</div>';
		return 0;
	}

	function printFieldListWhere($parameters, &$object, &$action, $hookmanager) {
		/*
		   If the search_customers_phones attribute is specified in the POST or GET request then limit the 
		   third parties return to those with a phone number that contains the given digits
		 */
		$search_term=trim(GETPOST("search_customers_phones"));
		if (empty($search_term))
			return "";
			
		$this->resprints = " AND (s.phone LIKE '%".$search_term."%' OR ef.mobilephone LIKE '%".$search_term."%')";
		return 1;
	}

	/**
	 * Hooks into login logic
	 */
	function updateSession($parameters, &$object, &$action, $hookmanager)
	{
		// echo "<!--#### updateSession()\n";
		// echo "action=".$action."\n";
		// echo "params="; print_r($parameters); echo "\n";
		// echo "object="; print_r($object); echo "\n";
		// echo "-->";

		if ($parameters['context'] == 'main')
		{
			$this->includeMainCustomizations($parameters, $object, $action, $hookmanager);
		}

		return 0;
	}

	/**
	 * Hooks into event listing on calendar page
	 */
	function getCalendarEvents($parameters, &$object, &$action, $hookmanager)
	{
		include(__DIR__."/../agenda/add-county-squares.js.php");
		include(__DIR__."/../agenda/remove-extra-week.js.php");
		include(__DIR__."/../agenda/use-old-background.js.php");
		include(__DIR__."/../agenda/remove-other-estimates.js.php");
	}

	function isEditOrCreate($action)
	{
		//print "###".$action."###";
		return in_array($action, ['edit', 'create']);
	}
	
	function includeThirdPartyCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		print '<tr style="display:none"><td>';
		
		// Only insert for new objects
		if ($object->id=="")
		{
			include(__DIR__."/../customers/default_state.js.php");
            
			if (GETPOST("type")=='c')  { include(__DIR__."/../customers/Default_Third_Party_Type.js.php"); }   //added by German Acosta 01/21/2018 INCLUDED ONLY IN A CASE IS A CUSTOMER
			if (GETPOST("type")=='c')  { include(__DIR__."/../customers/Building_Department_Loc.js.php"); } else {include(__DIR__."/../customers/Building_Department_Hide.js.php"); }   //added by German Acosta 01/21/2018 INCLUDED ONLY IN A CASE IS A CUSTOMER
			if (empty(GETPOST("type")))  { include(__DIR__."/../customers/Default_Vendor.js.php"); }
			
			include(__DIR__."/../customers/customer_name.js.php");
			include(__DIR__."/../customers/phone-number-masks.js.php");
     	}
		
		// insert only for objects being edited
		if ($this->isEditOrCreate($action))
		{
			include(__DIR__."/../customers/Building_Department_Loc.js.php");   //added by German Acosta 01/21/2018 INCLUDED ONLY IN A CASE IS A CUSTOMER	
			include(__DIR__."/../customers/phone-number-masks.js.php");
		}

		// insert only for objects being viewed
		if (!$this->isEditOrCreate($action))
		{
			include(__DIR__."/../customers/mobile_phone_view.js.php");
			include(__DIR__."/../customers/hide_vat_view.js.php");
			include(__DIR__."/../customers/add_builddept_url.js.php");
			include(__DIR__."/../customers/fix_create_event_link.js.php");
			include(__DIR__."/../customers/amreltype_view.js.php");
		}

		// insert for new objects or objects being edited
		if ($this->isEditOrCreate($action) || $object->id=="") {
			include(__DIR__."/../customers/secondary_email_edit.js.php");
			include(__DIR__."/../customers/mobile_phone_edit.js.php");
			include(__DIR__."/../customers/hide_vat_edit.js.php");
			include(__DIR__."/../customers/amreltype_edit.js.php");
			
			// Show City drop down
			include(__DIR__."/../customers/city_list.js.php");

			// Show Building Department City drop down
			include(__DIR__."/../customers/builddept_city_list.js.php");
		}
		print '</td></tr>';
	}
		
	function includeProductCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		print '<tr style="display:none"><td>';
		
		// Only insert for new objects
		if ($object->id=="")
		{
			include(__DIR__."/../products/default_units.js.php");
			include(__DIR__."/../products/default_job_type.js.php");
		}

		if ($action == "create" || $action == "edit") {
			include(__DIR__."/../products/hide_unwanted_fields_edit.js.php");
			include(__DIR__."/../products/product_dimensions_validation.js.php");
		}
		if ($action == "view" || $action == "") {
			include(__DIR__."/../products/hide_unwanted_fields_view.js.php");
		}
		
		print '</td></tr>';
	}
		
	function includeAgendaCardCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		print '<tr style="display:none"><td>';
		
		// insert for new objects or objects being edited
		if ($this->isEditOrCreate($action) || $object->id=="") {
			include(__DIR__."/../agenda/autofill_location.js.php");
			include(__DIR__."/../agenda/add-change-owner-buttons.js.php");
			include(__DIR__."/../agenda/autofill_enddate.js.php");
			include(__DIR__."/../agenda/autofill_type.js.php");
			include(__DIR__."/../agenda/add-working-day-radio-button.js.php");
			include(__DIR__."/../customers/expand-related-company-dropdown.js.php");
		}
		else
			include(__DIR__."/../agenda/add_contact_phones.js.php");
			
		print '</td></tr>';
	}

	function includeMainCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		$user = $object;

		// Check if we are in the initial page for creating a new event
		if (strpos($_SERVER['REQUEST_URI'], '/comm/action/card.php?action=create') !== false)
		{
			// Check if we got here from the calendar page or a page that predefines the event assignment
			if (strpos($_SERVER['HTTP_REFERER'], 'search_filtert=') !== false)
			{
				// Determine the predefined user (the filter for events in calendar if any)
				$qs = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_QUERY);
				$qsParams = array();
				parse_str($qs, $qsParams);
				$filtert = $qsParams["search_filtert"];
				if ($filtert && $filtert != "-1" && $filtert != $user->id)
				{
					$this->overrideEventAssignments($user, $qsParams["search_filtert"]);
				}
			}
		}
	}

	// When a new event is created for SHUTTER or IMPACT installations we want to override the users that the event is assigned to
	function overrideEventAssignments($user, $eventUserId)
	{
		$listofuserid=json_decode($_SESSION['assignedtouser'], true);
		if (!array_key_exists($eventUserId, $listofuserid))
		{
			$listofuserid=array(); // Replace current users which should only be the owner but we want to avoid old session content
			$listofuserid[$user->id] = array('id'=>$user->id, 'transparency'=>0, 'mandatory'=>1);
			$listofuserid[$eventUserId] = array('id'=>$eventUserId, 'transparency'=>0, 'mandatory'=>0);
			$_SESSION['assignedtouser'] = json_encode($listofuserid);
			$_POST['donotclearsession']=1;
		}
	}

	function includeContactCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		print '<tr style="display:none"><td>';
		
		// Only insert for new objects
		if ($object->id=="")
		{
			include(__DIR__."/../contact/default_state.js.php");
			include(__DIR__."/../contact/phone-number-masks.js.php");
		}
			
		// insert only for objects being edited
		if ($this->isEditOrCreate($action))
		{
			include(__DIR__."/../contact/phone-number-masks.js.php");
		}
		
		print '</td></tr>';
	}
}

?>