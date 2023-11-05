<?php

class Actionsamhpestimatesv2
{ 
	/**
	 * Called after building the array of tabs at the top of the third party pages.
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function completeTabsHead($parameters, &$object, &$action, $hookmanager)
	{
		// echo "<!--#### completeTabsHead()\n";
		// echo "action=".$action."\n";
		// echo "params="; print_r($parameters); echo "\n";
		// echo "object="; print_r($object); echo "\n";
		// echo "-->";
		
		if (strpos($parameters['context'], ':thirdpartycard:')
			|| strpos($parameters['context'], ':estimatesv2tptab:')
			|| strpos($parameters['context'], ':commcard:')
			|| strpos($parameters['context'], ':projectthirdparty:')
			|| strpos($parameters['context'], ':consumptionthirdparty:')
			|| strpos($parameters['context'], ':thirdpartybancard:')
			|| strpos($parameters['context'], ':thirdpartynotification:')
			|| strpos($parameters['context'], ':agendathirdparty:')
			)
		{
			return $this->includeThirdPartyCustomizations($parameters, $object, $action, $hookmanager);
		}
	}

	function includeThirdPartyCustomizations($parameters, &$object, &$action, $hookmanager)
	{
		global $db;
		$head = $parameters["head"];
		$mode = $parameters["mode"];
		$socid = $parameters["object"]->id;
		$updated = 0;
		if (!empty($head) && $mode == "add")
		{
			$headCount = count($parameters["head"]);
			for ($i=0; $i < $headCount; $i++) { 
				if ($head[$i][2] == "estimates")
				{
					$sql  = "SELECT count(*) as ecount";
					$sql .= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";
					$sql .= " WHERE e.customerid = ".$socid;
			
					$result=$db->query($sql);
					if ($result)
					{
						$obj = $db->fetch_object($result);
						$num = $obj->ecount;
						$db->free($result);

						if ($num > 0)
							$head[$i][1] = $head[$i][1] . " <span class=\"badge\">".$num."</span>";
						$updated = 1;
					}
					else
					{
						dol_print_error($db);
					}
				}
			}
		}
		if ($updated > 0)
			$hookmanager->resArray = $head;
		return $updated;
	}
		
}

?>