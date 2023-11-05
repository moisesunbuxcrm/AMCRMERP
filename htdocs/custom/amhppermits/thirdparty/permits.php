<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

$langs->load("amhppermits@amhppermits");
$langs->load("companies");

$socid = GETPOST('socid','int');

/*
 *	View
 */

$contactstatic = new Contact($db);

$form = new Form($db);

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('permitstptab'));

if ($socid)
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$langs->load("companies");

	$object = new Societe($db);
	$result = $object->fetch($socid);

	$title=$langs->trans("Permits");
	if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/thirdpartynameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->name." - ".$title;
	llxHeader('',$title);

	if (! empty($conf->notification->enabled)) $langs->load("mails");
	$head = societe_prepare_head($object);

	dol_fiche_head($head, 'permits', $langs->trans("ThirdParty"), -1, 'company');

    $linkback = '<a href="'.DOL_URL_ROOT.'/societe/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

    dol_banner_tab($object, 'socid', $linkback, ($user->societe_id?0:1), 'rowid', 'nom');

    print '<div class="fichecenter">';

    print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">';

    if (! empty($conf->global->SOCIETE_USEPREFIX))  // Old not used prefix field
    {
        print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$object->prefix_comm.'</td></tr>';
    }

	if ($object->client)
	{
		print '<tr><td class="titlefield">';
		print $langs->trans('CustomerCode').'</td><td colspan="3">';
		print $object->code_client;
		if ($object->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
		print '</td></tr>';
	}

	if ($object->fournisseur)
	{
		print '<tr><td class="titlefield">';
		print $langs->trans('SupplierCode').'</td><td colspan="3">';
		print $object->code_fournisseur;
		if ($object->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
		print '</td></tr>';
	}

	print '</table>';

	print '</div>';

	dol_fiche_end();


    /*
     * Barre d'action
     */

    print '<br>';


	// Estimates list
	//$result=show_projects($conf, $langs, $db, $object, $_SERVER["PHP_SELF"].'?socid='.$object->id, 1, $addbutton);

    $i = -1 ;

    if ($user->rights->amhppermits->read)
    {
        $buttoncreate='';
        if ($user->rights->amhppermits->write)
        {
			$buttoncreate='<a class="addnewrecord" href="'.DOL_URL_ROOT.'/custom/amhppermits/buildingpermit_card.php?action=createFromSocid&socid='.$socid.'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.$langs->trans("AMHPAddPermit");
			if (empty($conf->dol_optimize_smallscreen)) $buttoncreate.=' '.img_picto($langs->trans("AMHPAddPermit"),'filenew');
			$buttoncreate.='</a>'."\n";
        }

        print "\n";
        print load_fiche_titre($langs->trans("AMHPPermitsForThisThirdParty"), $buttoncreate.$morehtmlright, '');
        print '<div class="div-table-responsive">';
        print "\n".'<table class="noborder" width=100%>';

        $sql  = 'SELECT bp.rowid, bp.date_creation, bp.ref, bp.label, po.poid, po.ponumber, e.id as eid, e.estimatenum
	        FROM `llx_amhppermits_buildingpermit` bp
            left JOIN llx_ea_po po on bp.poid=po.poid
            left JOIN llx_ea_estimate e on bp.eid=e.id
            WHERE 
                bp.fk_soc = '.$socid;

        $result=$db->query($sql);
        if ($result)
        {
            $num = $db->num_rows($result);

            print '<tr class="liste_titre">';
            print '<td>Date</td>';
            print '<td>Estimate</td>';
            print '<td>Permit</td>';
            print '</tr>';

            if ($num > 0)
            {
                $i=0;
                while ($i < $num)
                {
                    $obj = $db->fetch_array($result);
                    {
                        print '<tr class="oddeven">';

                        // Permit id
                        $permitId = $obj['rowid'];
                        // Date
                        print '<td>'.dol_print_date($db->jdate($obj['date_creation']),"day").'</td>';
                        // Estimate
                        if ($obj['poid'] != '')
                            print '<td><a href="'.DOL_URL_ROOT.'/custom/amhpestimates/card.php?poid='.$obj['poid'].'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.img_object($langs->trans("AMHPShowEstimate"),'projectpub')." ".$obj['ponumber'].'</a></td>';
                        if ($obj['eid'] != '')
                            print '<td><a href="'.DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php?eid='.$obj['eid'].'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.img_object($langs->trans("AMHPShowEstimate"),'projectpub')." ".$obj['estimatenum'].'</a></td>';

                        // Permit ref
                        print '<td><a href="'.DOL_URL_ROOT.'/custom/amhppermits/buildingpermit_card.php?id='.$permitId.'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.img_object($langs->trans("AMHPShowEstimate"),'projectpub')." ".$obj['ref'].'</a></td>';

                        print '</tr>';
                    }
                    $i++;
                }
            }
            else
			{
            	print '<tr class="oddeven"><td colspan="5" class="opacitymedium">'.$langs->trans("None").'</td></tr>';
            }
            $db->free($result);
        }
        else
        {
            dol_print_error($db);
        }
        print "</table>";
        print '</div>';

        print "<br>\n";
    }

}


llxFooter();

$db->close();
