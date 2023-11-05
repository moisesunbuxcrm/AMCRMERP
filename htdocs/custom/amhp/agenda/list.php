<?php
/* COPIED from comm&action&list.php and modified to provide SEARCH EVENTS function 
 *
 * Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2004-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2017      Open-DSI             <support@open-dsi.fr>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/comm/action/list.php
 *      \ingroup    agenda
 *		\brief      Page to list actions
 */

if (!defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/agenda.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

// Load translation files required by the page
$langs->loadLangs(array("users", "companies", "agenda", "commercial", "other"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$object = new ActionComm($db);
$hookmanager->initHooks(array('agendalist'));

$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$limit = $conf->liste_limit;
$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if ($page == -1 || $page == null) { $page = 0; }
$offset = $limit * $page;
if (!$sortorder)
{
	$sortorder = "DESC,DESC";
}
if (!$sortfield)
{
	$sortfield = "a.datep,a.id";
}
$custom_search_events=trim(GETPOST("custom_search_events"));

// Security check
$canedit = 1;
if (!$user->rights->agenda->myactions->read) accessforbidden();
if (!$user->rights->agenda->allactions->read) $canedit = 0;

$arrayfields = array(
	'a.id'=>array('label'=>"Ref", 'checked'=>1),
	'owner'=>array('label'=>"Owner", 'checked'=>1),
	'c.libelle'=>array('label'=>"Type", 'checked'=>1),
	'a.label'=>array('label'=>"Title", 'checked'=>1),
	'a.note'=>array('label'=>'Description', 'checked'=>0),
	'a.datep'=>array('label'=>"DateStart", 'checked'=>1),
	'a.datep2'=>array('label'=>"DateEnd", 'checked'=>1),
	's.nom'=>array('label'=>"ThirdParty", 'checked'=>1),
	'a.fk_contact'=>array('label'=>"Contact", 'checked'=>1),
	'a.fk_element'=>array('label'=>"LinkedObject", 'checked'=>0, 'enabled'=>(!empty($conf->global->AGENDA_SHOW_LINKED_OBJECT))),
	'a.percent'=>array('label'=>"Status", 'checked'=>1, 'position'=>1000),
	'a.datec'=>array('label'=>'DateCreation', 'checked'=>0),
	'a.tms'=>array('label'=>'DateModification', 'checked'=>0)
);
// Extra fields
if (is_array($extrafields->attributes[$object->table_element]['label']) && count($extrafields->attributes[$object->table_element]['label']) > 0)
{
	foreach ($extrafields->attributes[$object->table_element]['label'] as $key => $val)
	{
		if (!empty($extrafields->attributes[$object->table_element]['list'][$key]))
			$arrayfields["ef.".$key] = array('label'=>$extrafields->attributes[$object->table_element]['label'][$key], 'checked'=>(($extrafields->attributes[$object->table_element]['list'][$key] < 0) ? 0 : 1), 'position'=>$extrafields->attributes[$object->table_element]['pos'][$key], 'enabled'=>(abs($extrafields->attributes[$object->table_element]['list'][$key]) != 3 && $extrafields->attributes[$object->table_element]['perms'][$key]));
	}
}
$object->fields = dol_sort_array($object->fields, 'position');
$arrayfields = dol_sort_array($arrayfields, 'position');

/*
 *  View
 */

$userstatic = new User($db);

$now = dol_now();

$help_url = 'EN:Module_Agenda_En|FR:Module_Agenda|ES:M&omodulodulo_Agenda';
llxHeader('', $langs->trans("Agenda"), $help_url);

$param = '';
if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit='.urlencode($limit);
if (!empty($custom_search_events)) $param .= '&custom_search_events='.urlencode($custom_search_events);

$sql = "SELECT";
if ($usergroup > 0) $sql .= " DISTINCT";
$sql .= " s.nom as societe, s.rowid as socid, s.client, s.email as socemail,";
$sql .= " a.id, a.code, a.label, a.note, a.datep as dp, a.datep2 as dp2, a.fulldayevent, a.location,";
$sql .= ' a.fk_user_author,a.fk_user_action,';
$sql .= " a.fk_contact, a.note, a.percent as percent,";
$sql .= " a.fk_element, a.elementtype, a.datec, a.tms as datem,";
$sql .= " c.code as type_code, c.libelle as type_label,";
$sql .= " sp.lastname, sp.firstname, sp.email, sp.phone, sp.address, sp.phone as phone_pro, sp.phone_mobile, sp.phone_perso, sp.fk_pays as country_id";

// Add fields from extrafields
if (!empty($extrafields->attributes[$object->table_element]['label'])) {
	foreach ($extrafields->attributes[$object->table_element]['label'] as $key => $val) $sql .= ($extrafields->attributes[$object->table_element]['type'][$key] != 'separate' ? ", ef.".$key.' as options_'.$key : '');
}

// Add fields from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListSelect', $parameters); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;

$sql .= " FROM ".MAIN_DB_PREFIX."actioncomm as a";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."actioncomm_extrafields as ef ON (a.id = ef.fk_object) ";
if (!$user->rights->societe->client->voir && !$socid) $sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON a.fk_soc = sc.fk_soc";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON a.fk_soc = s.rowid";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe_extrafields as sef ON s.rowid = sef.fk_object"; // ADDED pdermody
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."socpeople as sp ON a.fk_contact = sp.rowid";
$sql .= " ,".MAIN_DB_PREFIX."c_actioncomm as c";
$sql .= " WHERE c.id = a.fk_action";
$sql .= ' AND a.entity IN ('.getEntity('agenda').')';
if (!$user->rights->societe->client->voir && !$socid) $sql .= " AND (a.fk_soc IS NULL OR sc.fk_user = ".$user->id.")";

if (!empty($custom_search_events)) 
{
	$sql .= " AND ((s.phone LIKE '%".$custom_search_events."%' OR sef.mobilephone LIKE '%".$custom_search_events."%')";
	$sql .= " OR (s.nom LIKE '%".$custom_search_events."%'))";
}

// Add where from extra fields
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_sql.tpl.php';

// Add where from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListWhere', $parameters); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;

$sql .= $db->order($sortfield, $sortorder);

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
    if (($page * $limit) > $nbtotalofrecords)	// if total resultset is smaller then paging size (filtering), goto and load page 0
    {
    	$page = 0;
    	$offset = 0;
    }
}

$sql .= $db->plimit($limit + 1, $offset);
//print $sql;

dol_syslog("comm/action/list.php", LOG_DEBUG);
$resql = $db->query($sql);
if ($resql)
{
	$actionstatic = new ActionComm($db);
	$societestatic = new Societe($db);

	$num = $db->num_rows($resql);

	$tabactive = 'cardlist';

	$head = calendars_prepare_head($param);

    print '<table class="tagtable liste'.($moreforfilter ? " listwithfilterbefore" : "").'">'."\n";

	print '<tr class="liste_titre">';
	if (!empty($arrayfields['a.id']['checked']))	      print_liste_field_titre($arrayfields['a.id']['label'], $_SERVER["PHP_SELF"], "a.id", $param, "", "", $sortfield, $sortorder);
	if (!empty($arrayfields['owner']['checked']))        print_liste_field_titre($arrayfields['owner']['label'], $_SERVER["PHP_SELF"], "", $param, "", "", $sortfield, $sortorder);
	if (!empty($arrayfields['c.libelle']['checked']))	  print_liste_field_titre($arrayfields['c.libelle']['label'], $_SERVER["PHP_SELF"], "c.libelle", $param, "", "", $sortfield, $sortorder);
	if (!empty($arrayfields['a.label']['checked']))	  print_liste_field_titre($arrayfields['a.label']['label'], $_SERVER["PHP_SELF"], "a.label", $param, "", "", $sortfield, $sortorder);
	if (!empty($arrayfields['a.note']['checked']))		  print_liste_field_titre($arrayfields['a.note']['label'], $_SERVER["PHP_SELF"], "a.note", $param, "", "", $sortfield, $sortorder);
	//if (! empty($conf->global->AGENDA_USE_EVENT_TYPE))
	if (!empty($arrayfields['a.datep']['checked']))	  print_liste_field_titre($arrayfields['a.datep']['label'], $_SERVER["PHP_SELF"], "a.datep,a.id", $param, '', 'align="center"', $sortfield, $sortorder);
	if (!empty($arrayfields['a.datep2']['checked']))	  print_liste_field_titre($arrayfields['a.datep2']['label'], $_SERVER["PHP_SELF"], "a.datep2", $param, '', 'align="center"', $sortfield, $sortorder);
	if (!empty($arrayfields['s.nom']['checked']))	      print_liste_field_titre($arrayfields['s.nom']['label'], $_SERVER["PHP_SELF"], "s.nom", $param, "", "", $sortfield, $sortorder);
	if (!empty($arrayfields['a.fk_contact']['checked'])) print_liste_field_titre($arrayfields['a.fk_contact']['label'], $_SERVER["PHP_SELF"], "", $param, "", "", $sortfield, $sortorder);
    if (!empty($arrayfields['a.fk_element']['checked'])) print_liste_field_titre($arrayfields['a.fk_element']['label'], $_SERVER["PHP_SELF"], "", $param, "", "", $sortfield, $sortorder);

	// Extra fields
    include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_title.tpl.php';

	// Hook fields
	$parameters = array('arrayfields'=>$arrayfields, 'param'=>$param, 'sortfield'=>$sortfield, 'sortorder'=>$sortorder);
	$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	if (!empty($arrayfields['a.datec']['checked'])) print_liste_field_titre($arrayfields['a.datec']['label'], $_SERVER["PHP_SELF"], "a.datec,a.id", $param, "", 'align="center"', $sortfield, $sortorder);
	if (!empty($arrayfields['a.tms']['checked'])) print_liste_field_titre($arrayfields['a.tms']['label'], $_SERVER["PHP_SELF"], "a.tms,a.id", $param, "", 'align="center"', $sortfield, $sortorder);

	if (!empty($arrayfields['a.percent']['checked']))print_liste_field_titre("Status", $_SERVER["PHP_SELF"], "a.percent", $param, "", 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"], "", '', '', 'align="center"', $sortfield, $sortorder, 'maxwidthsearch ');
	print "</tr>\n";

	$contactstatic = new Contact($db);
	$now = dol_now();
	$delay_warning = $conf->global->MAIN_DELAY_ACTIONS_TODO * 24 * 60 * 60;

	require_once DOL_DOCUMENT_ROOT.'/comm/action/class/cactioncomm.class.php';
	$caction = new CActionComm($db);
	$arraylist = $caction->liste_array(1, 'code', '', (empty($conf->global->AGENDA_USE_EVENT_TYPE) ? 1 : 0), '', 1);
    $contactListCache = array();

	while ($i < min($num, $limit))
	{
		$obj = $db->fetch_object($resql);

        // Discard auto action if option is on
        if (!empty($conf->global->AGENDA_ALWAYS_HIDE_AUTO) && $obj->type_code == 'AC_OTH_AUTO')
        {
        	$i++;
        	continue;
        }

		$actionstatic->id = $obj->id;
		$actionstatic->ref = $obj->id;
		$actionstatic->code = $obj->code;
		$actionstatic->type_code = $obj->type_code;
		$actionstatic->type_label = $obj->type_label;
		$actionstatic->type_picto = $obj->type_picto;
		$actionstatic->label = $obj->label;
		$actionstatic->location = $obj->location;
		$actionstatic->note_private = dol_htmlentitiesbr($obj->note);

		$actionstatic->fetchResources();

		print '<tr class="oddeven">';

		// Ref
		if (!empty($arrayfields['a.id']['checked'])) {
			print '<td>';
			print $actionstatic->getNomUrl(1, -1);
			print '</td>';
		}

		// User owner
		if (!empty($arrayfields['owner']['checked']))
		{
			print '<td class="tdoverflowmax150">'; // With edge and chrome the td overflow is not supported correctly when content is not full text.
			if ($obj->fk_user_action > 0)
			{
				$userstatic->fetch($obj->fk_user_action);
				print $userstatic->getNomUrl(-1);
			}
			else print '&nbsp;';
			print '</td>';
		}

		// Type
		if (!empty($arrayfields['c.libelle']['checked']))
		{
			print '<td class="nowraponall">';
			$actioncomm = $actionstatic;
			// TODO Code common with code into showactions
			$imgpicto = '';
			if (!empty($conf->global->AGENDA_USE_EVENT_TYPE))
			{
				if ($actioncomm->type_picto) {
					$imgpicto = img_picto('', $actioncomm->type_picto);
				}
				else {
					if ($actioncomm->type_code == 'AC_RDV')         $imgpicto = img_picto('', 'object_group', '', false, 0, 0, '', 'paddingright').' ';
					elseif ($actioncomm->type_code == 'AC_TEL')     $imgpicto = img_picto('', 'object_phoning', '', false, 0, 0, '', 'paddingright').' ';
					elseif ($actioncomm->type_code == 'AC_FAX')     $imgpicto = img_picto('', 'object_phoning_fax', '', false, 0, 0, '', 'paddingright').' ';
					elseif ($actioncomm->type_code == 'AC_EMAIL')   $imgpicto = img_picto('', 'object_email', '', false, 0, 0, '', 'paddingright').' ';
					elseif ($actioncomm->type_code == 'AC_INT')     $imgpicto = img_picto('', 'object_intervention', '', false, 0, 0, '', 'paddingright').' ';
					elseif ($actioncomm->type_code == 'AC_OTH' && $actioncomm->code == 'TICKET_MSG') $imgpicto = img_picto('', 'object_conversation', '', false, 0, 0, '', 'paddingright').' ';
					elseif (!preg_match('/_AUTO/', $actioncomm->type_code)) $imgpicto = img_picto('', 'object_other', '', false, 0, 0, '', 'paddingright').' ';
				}
			}
			print $imgpicto;

			$labeltype = $obj->type_code;
			if (empty($conf->global->AGENDA_USE_EVENT_TYPE) && empty($arraylist[$labeltype])) $labeltype = 'AC_OTH';
			if ($actioncomm->type_code == 'AC_OTH' && $actioncomm->code == 'TICKET_MSG') {
				$labeltype = $langs->trans("Message");
			} elseif (!empty($arraylist[$labeltype])) $labeltype = $arraylist[$labeltype];
			print dol_trunc($labeltype, 28);
			print '</td>';
		}

		// Label
		if (!empty($arrayfields['a.label']['checked'])) {
			print '<td class="tdoverflowmax200">';
			print $actionstatic->label;
			print '</td>';
		}

		// Description
		if (!empty($arrayfields['a.note']['checked'])) {
			print '<td class="tdoverflowonsmartphone">';
			$text = dolGetFirstLineOfText(dol_string_nohtmltag($actionstatic->note_private, 0));
			print $form->textwithtooltip(dol_trunc($text, 40), $actionstatic->note_private);
			print '</td>';
		}

		$formatToUse = $obj->fulldayevent ? 'day' : 'dayhour';
		// Start date
		if (!empty($arrayfields['a.datep']['checked'])) {
			print '<td class="center nowraponall">';
			print dol_print_date($db->jdate($obj->dp), $formatToUse);
			$late = 0;
			if ($obj->percent == 0 && $obj->dp && $db->jdate($obj->dp) < ($now - $delay_warning)) $late = 1;
			if ($obj->percent == 0 && !$obj->dp && $obj->dp2 && $db->jdate($obj->dp) < ($now - $delay_warning)) $late = 1;
			if ($obj->percent > 0 && $obj->percent < 100 && $obj->dp2 && $db->jdate($obj->dp2) < ($now - $delay_warning)) $late = 1;
			if ($obj->percent > 0 && $obj->percent < 100 && !$obj->dp2 && $obj->dp && $db->jdate($obj->dp) < ($now - $delay_warning)) $late = 1;
			if ($late) print img_warning($langs->trans("Late")).' ';
			print '</td>';
		}

		// End date
		if (!empty($arrayfields['a.datep2']['checked'])) {
			print '<td class="center nowraponall">';
			print dol_print_date($db->jdate($obj->dp2), $formatToUse);
			print '</td>';
		}

		// Third party
		if (!empty($arrayfields['s.nom']['checked'])) {
			print '<td class="tdoverflowmax150">';
			if ($obj->socid > 0)
			{
				$societestatic->id = $obj->socid;
				$societestatic->client = $obj->client;
				$societestatic->name = $obj->societe;
				$societestatic->email = $obj->socemail;

				print $societestatic->getNomUrl(1, '', 28);
			}
			else print '&nbsp;';
			print '</td>';
		}

		// Contact
		if (!empty($arrayfields['a.fk_contact']['checked'])) {
			print '<td class="tdoverflowmax100">';

            if (!empty($actionstatic->socpeopleassigned))
            {
                $contactList = array();
                foreach ($actionstatic->socpeopleassigned as $socpeopleassigned)
                {
                    if (!isset($contactListCache[$socpeopleassigned['id']]))
                    {
                        // if no cache found we fetch it
                        $contact = new Contact($db);
                        if ($contact->fetch($socpeopleassigned['id']) > 0)
                        {
                            $contactListCache[$socpeopleassigned['id']] = $contact->getNomUrl(1, '', 0);
                            $contactList[] = $contact->getNomUrl(1, '', 0);
                        }
                    }
                    else {
                        // use cache
                        $contactList[] = $contactListCache[$socpeopleassigned['id']];
                    }
                }
                if (!empty($contactList)) {
                    print implode(', ', $contactList);
                }
            }
            elseif ($obj->fk_contact > 0) //keep for retrocompatibility with faraway event
			{
				$contactstatic->id = $obj->fk_contact;
				$contactstatic->email = $obj->email;
				$contactstatic->lastname = $obj->lastname;
				$contactstatic->firstname = $obj->firstname;
				$contactstatic->phone_pro = $obj->phone_pro;
				$contactstatic->phone_mobile = $obj->phone_mobile;
				$contactstatic->phone_perso = $obj->phone_perso;
				$contactstatic->country_id = $obj->country_id;
				print $contactstatic->getNomUrl(1, '', 0);
			}
			else
			{
				print "&nbsp;";
			}
			print '</td>';
		}

		// Linked object
		if (!empty($arrayfields['a.fk_element']['checked'])) {
            print '<td class="tdoverflowmax150">';
            //var_dump($obj->fkelement.' '.$obj->elementtype);
            if ($obj->fk_element > 0 && !empty($obj->elementtype)) {
                include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
                print dolGetElementUrl($obj->fk_element, $obj->elementtype, 1);
            } else {
                print "&nbsp;";
            }
            print '</td>';
		}

		// Extra fields
		include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_print_fields.tpl.php';
		// Fields from hook
		$parameters = array('arrayfields'=>$arrayfields, 'obj'=>$obj, 'i'=>$i, 'totalarray'=>&$totalarray);
		$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters); // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		// Date creation
		if (!empty($arrayfields['a.datec']['checked'])) {
			// Status/Percent
			print '<td align="center" class="nowrap">'.dol_print_date($db->jdate($obj->datec), 'dayhour').'</td>';
		}
		// Date update
		if (!empty($arrayfields['a.tms']['checked'])) {
			print '<td align="center" class="nowrap">'.dol_print_date($db->jdate($obj->datem), 'dayhour').'</td>';
		}
		if (!empty($arrayfields['a.percent']['checked'])) {
			// Status/Percent
			$datep = $db->jdate($obj->datep);
			print '<td align="center" class="nowrap">'.$actionstatic->LibStatut($obj->percent, 5, 0, $datep).'</td>';
		}
		print '<td></td>';

		print "</tr>\n";
		$i++;
	}
	print "</table>";
    print '</div>';

	$db->free($resql);
}
else
{
	dol_print_error($db);
}

// End of page
llxFooter();
$db->close();
