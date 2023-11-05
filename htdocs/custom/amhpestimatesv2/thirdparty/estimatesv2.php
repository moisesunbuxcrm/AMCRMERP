<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

$langs->load("amhpestimatesv2@amhpestimatesv2");
$langs->load("companies");

$socid = GETPOST('socid','int');

/*
 *	View
 */

$contactstatic = new Contact($db);

$form = new Form($db);

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('estimatesv2tptab'));

if ($socid)
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$langs->load("companies");

	$object = new Societe($db);
	$result = $object->fetch($socid);

	$title=$langs->trans("Estimates");
	if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/thirdpartynameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->name." - ".$title;
	llxHeader('',$title);

	if (! empty($conf->notification->enabled)) $langs->load("mails");
	$head = societe_prepare_head($object);

	dol_fiche_head($head, 'estimatesv2', $langs->trans("ThirdParty"), -1, 'company');

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


	// estimatesv2 list
	//$result=show_projects($conf, $langs, $db, $object, $_SERVER["PHP_SELF"].'?socid='.$object->id, 1, $addbutton);

    $i = -1 ;

    if ($user->rights->amhpestimatesv2->estimates->read)
    {
        $buttoncreate='';
        if ($user->rights->amhpestimatesv2->estimates->create)
        {
			$buttoncreate='<a class="addnewrecord" href="'.DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php?socid='.$socid.'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.$langs->trans("AMHPAddEstimate");
			if (empty($conf->dol_optimize_smallscreen)) $buttoncreate.=' '.img_picto($langs->trans("AMHPAddEstimate"),'filenew');
			$buttoncreate.='</a>'."\n";
        }

        print "\n";
        print load_fiche_titre($langs->trans("AMHPEstmatesForThisThirdParty"), $buttoncreate.$morehtmlright, '');
        print '<div class="div-table-responsive">';
        print "\n".'<table class="noborder" width=100%>';

        $sql  = "SELECT e.rowid, e.estimatenum, e.quotedate, e.vendor, 0 as SALESPRICE";
        $sql .= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";
        $sql .= " WHERE e.customerid = ".$socid;
        $sql .= " ORDER BY e.quotedate DESC";

        $result=$db->query($sql);
        if ($result)
        {
            $num = $db->num_rows($result);

            print '<tr class="liste_titre">';
            print '<td>Estimate</td>';
            print '<td>Date</td>';
            print '<td>Vendor</td>';
            print '<td class="right">Price</td>';
            print '</tr>';

            if ($num > 0)
            {
				require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/EaEstimate.class.php';

                $e = new EaEstimate($db);

                $i=0;

                while ($i < $num)
                {
                    $obj = $db->fetch_object($result);
                    {
                        print '<tr class="oddeven">';

                        print '<td><a href="'.DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php?id='.$obj->rowid.'&socid='.$socid.'&backtopage='.urlencode($_SERVER["PHP_SELF"].'?socid='.$object->id).'">'.img_object($langs->trans("AMHPShowEstimate"),'projectpub')." ".$obj->estimatenum.'</a></td>';
                        print '<td>'.dol_print_date($db->jdate($obj->quotedate),"day").'</td>';
                        print '<td>'.$obj->vendor.'</td>';
                        print '<td class="right">'.price($obj->SALESPRICE, 1, '', 1, -1, -1, '').'</td>';

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
