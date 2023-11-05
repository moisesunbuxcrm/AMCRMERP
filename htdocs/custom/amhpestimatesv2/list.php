<?php
/* 
 * Based on same file from Third Parties
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimatesv2/class/eaestimate.class.php';

$langs->load("amhp");
$langs->load("amhpestimatesv2");

llxHeader("",$langs->trans("AHMPEstimatesArea"));

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

// Security check
if (! $user->rights->amhpestimatesv2->estimates->read) accessforbidden();

$search_all=trim(GETPOST('sall', 'alphanohtml'));
$search_cti=preg_replace('/^0+/', '', preg_replace('/[^0-9]/', '', GETPOST('search_cti', 'alphanohtml')));	// Phone number without any special chars

$search_estimatenum=trim(GETPOST('search_estimatenum'));
$search_customername=trim(GETPOST("search_customername"));
$search_customerphone=trim(GETPOST('search_customerphone'));
$search_customercity=trim(GETPOST("search_customercity"));
$search_customerstate=trim(GETPOST("search_customerstate"));
$search_customerzip=trim(GETPOST("search_customerzip"));
$search_customeremail=trim(GETPOST('search_customeremail'));

$mode=GETPOST("mode");
$diroutputmassaction=$conf->societe->dir_output . '/temp/massgeneration/'.$user->id;

$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$sortfield=GETPOST("sortfield",'alpha');
$sortorder=GETPOST("sortorder",'alpha');
$page=GETPOST("page",'int');
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="e.quotedate";
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	'tp.nom'=>"Name",
	'tp.town'=>"City",
	'e.estimatenum'=>"Estimate #",
	'tp.phone'=>"Phone",
);

// Define list of fields to show into list
$arrayfields=array(
    'e.estimatenum'=>array('label'=>"Estimate #", 'checked'=>1),
    'tp.nom'=>array('label'=>"Name", 'checked'=>1),
    'tp.phone'=>array('label'=>"Phone", 'checked'=>1),
    'tp.town'=>array('label'=>"City", 'checked'=>1),
    'tp.zip'=>array('label'=>"Zip", 'checked'=>0),
    'state.nom'=>array('label'=>"State", 'checked'=>0),
    'tp.email'=>array('label'=>"Email", 'checked'=>0),
);

/*
 * Actions
 */

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
//if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Did we click on purge search criteria ?
	if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') || GETPOST('button_removefilter','alpha')) // All tests are required to be compatible with all browsers
	{
		$search_estimatenum='';
		$search_customername='';
		$search_customercity="";
		$search_customerstate="";
		$search_customerzip="";
		$search_customeremail='';
		$search_customerphone='';
		$toselect='';
		$search_array_options=array();
	}

	// Mass actions
	$objectclass='EaEstimate';
	$objectlabel='Estimate';
	$permtoread = $user->rights->amhpestimatesv2->estimates->read;
	$permtodelete = $user->rights->amhpestimatesv2->estimates->update;
	$uploaddir = $conf->societe->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}

/*
 * View
 */

$form=new Form($db);

$title=$langs->trans("AMHPESTIMATESListOfPOsPageHeading");

$sql = "SELECT e.id, e.estimatenum, e.quotedate, tp.rowid as socid, tp.nom as customername, tp.town as customercity, tp.zip as customercity, ";
$sql.= " tp.email as customeremail, tp.phone as customerphone,";
$sql.= " state.code_departement as state_code, state.nom as state_name";
$sql.= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = e.customerid)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as state on (state.rowid = tp.fk_departement)";
$sql.= " WHERE 1=1 ";

if ($search_all)                    $sql.= natural_search(array_keys($fieldstosearchall), $search_all);
if (strlen($search_cti))            $sql.= natural_search('customerphone', $search_cti);

if ($search_estimatenum)            $sql.= natural_search("e.estimatenum",$search_estimatenum);
if ($search_customername)           $sql.= natural_search("tp.nom",$search_customername);
if ($search_customercity)           $sql.= natural_search("tp.town",$search_customercity);
if ($search_customerstate)          $sql.= natural_search("state.nom",$search_customerstate);
if (strlen($search_customerzip))    $sql.= natural_search("tp.zip",$search_customerzip);
if ($search_customeremail)          $sql.= natural_search("tp.email",$search_customeremail);
if (strlen($search_customerphone))  $sql.= natural_search("tp.phone", $search_customerphone);

$sql.= $db->order($sortfield,$sortorder);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1, $offset);

$resql = $db->query($sql);
if (! $resql)
{
    dol_print_error($db);
    exit;
}

$num = $db->num_rows($resql);

$arrayofselected=is_array($toselect)?$toselect:array();

if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && ($search_all != '' || $search_cti != '') && $action != 'list')
{
    $obj = $db->fetch_object($resql);
    $id = $obj->id;
    header("Location: ".DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php/'.$id);
    exit;
}

$param='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_all != '') $param = "&sall=".urlencode($search_all);
if ($sall != '') $param .= "&sall=".urlencode($sall);
if ($search_estimatenum != '') $param.= "&search_estimatenum=".urlencode($search_estimatenum);
if ($search_customername != '') $param.= "&search_customername=".urlencode($search_customername);
if ($search_customercity != '') $param.= "&search_customercity=".urlencode($search_customercity);
if ($search_customerstate != '') $param.= "&search_customerstate=".urlencode($search_customerstate);
if ($search_customerzip != '') $param.= "&search_customerzip=".urlencode($search_customerzip);
if ($search_customeremail != '') $param.= "&search_customeremail=".urlencode($search_customeremail);
if ($search_customerphone != '') $param.= "&search_customerphone=".urlencode($search_customerphone);
// Show delete result message
if (GETPOST('delpo'))
{
    setEventMessages($langs->trans("AMHPBuildingDepartmentDeleted",GETPOST('delpo')), null, 'mesgs');
}

// List of mass actions available
$arrayofmassactions =  array(
//    'presend'=>$langs->trans("SendByMail"),
//    'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->amhpestimatesv2->estimates->update) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" name="formfilter">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="page" value="'.$page.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($search_all)
{
    foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
    print $langs->trans("FilterOnInto", $search_all) . join(', ',$fieldstosearchall);
}

$varpage=$_SERVER["PHP_SELF"];
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
if ($massactionbutton) $selectedfields.=$form->showCheckAddButtons('checkforselect', 1);

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title search
print '<tr class="liste_titre_filter">';
if (! empty($arrayfields['e.estimatenum']['checked']))
{
    // estimatenum
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_estimatenum" value="'.dol_escape_htmltag($search_estimatenum).'">';
	print '</td>';
}
if (! empty($arrayfields['tp.nom']['checked']))
{
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" type="text" name="search_customername" size="8" value="'.dol_escape_htmltag($search_customername).'">';
	print '</td>';
}
if (! empty($arrayfields['tp.phone']['checked']))
{
    // Phone
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_customerphone" value="'.dol_escape_htmltag($search_customerphone).'">';
	print '</td>';
}
// Town
if (! empty($arrayfields['tp.town']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="6" type="text" name="search_customercity" value="'.dol_escape_htmltag($search_customercity).'">';
	print '</td>';
}
// Zip
if (! empty($arrayfields['tp.zip']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_customerzip" value="'.dol_escape_htmltag($search_customerzip).'">';
	print '</td>';
}
// State
if (! empty($arrayfields['state.nom']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_customerstate" value="'.dol_escape_htmltag($search_customerstate).'">';
	print '</td>';
}
if (! empty($arrayfields['tp.email']['checked']))
{
    // Email
	print '<td class="liste_titre">';
	print '<input class="flat searchemail" size="4" type="text" name="search_customeremail" value="'.dol_escape_htmltag($search_customeremail).'">';
	print '</td>';
}
// Action column
print '<td class="liste_titre" align="right">';
$searchpicto=$form->showFilterButtons();
print $searchpicto;
print '</td>';

print "</tr>\n";

print '<tr class="liste_titre">';
if (! empty($arrayfields['e.estimatenum']['checked']))			print_liste_field_titre($arrayfields['e.estimatenum']['label'],$_SERVER["PHP_SELF"],"e.estimatenum","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['tp.nom']['checked']))				print_liste_field_titre($arrayfields['tp.nom']['label'],$_SERVER["PHP_SELF"],"tp.nom","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['tp.phone']['checked']))			print_liste_field_titre($arrayfields['tp.phone']['label'],$_SERVER["PHP_SELF"],"tp.phone","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['tp.town']['checked']))				print_liste_field_titre($arrayfields['tp.town']['label'],$_SERVER["PHP_SELF"],"tp.town","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['tp.zip']['checked']))				print_liste_field_titre($arrayfields['tp.zip']['label'],$_SERVER["PHP_SELF"],"tp.zip","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['state.nom']['checked']))			print_liste_field_titre($arrayfields['state.nom']['label'],$_SERVER["PHP_SELF"],"state.nom","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['tp.email']['checked']))			print_liste_field_titre($arrayfields['tp.email']['label'],$_SERVER["PHP_SELF"],"tp.email","",$param,'',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="center"',$sortfield,$sortorder,'maxwidthsearch ');
print "</tr>\n";


$i = 0;
$totalarray=array();
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	print '<tr class="oddeven">';
	// estimatenum
    if (! empty($arrayfields['e.estimatenum']['checked']))
    {
        print '<td><a href="../../custom/amhpestimatesv2/card.php/'.$obj->id.'?mainmenu=amhpestimatesv2"> '.$obj->estimatenum.'</a></td>'."\n";
        if (! $i) $totalarray['nbfield']++;
    }
	if (! empty($arrayfields['tp.nom']['checked']))
	{
        print '<td class="tdoverflowmax200">';
        print '<a href="../../societe/card.php?socid='.$obj->socid.'"> '.$obj->customername.'</a>';
		print "</td>\n";
        if (! $i) $totalarray['nbfield']++;
	}
    if (! empty($arrayfields['tp.phone']['checked']))
    {
        print "<td data-mask='(000) 000-0000'>".$obj->phone."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
	// Town
    if (! empty($arrayfields['tp.town']['checked']))
    {
        print "<td>".$obj->town."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    // Zip
    if (! empty($arrayfields['tp.zip']['checked']))
    {
        print "<td>".$obj->zip."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    // State
    if (! empty($arrayfields['state.nom']['checked']))
    {
        print "<td>".$obj->state_name."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    if (! empty($arrayfields['tp.email']['checked']))
    {
        print "<td>".$obj->email."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }

    // Action column
    print '<td class="nowrap" align="center">';
    if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
    {
        $selected=0;
		if (in_array($obj->id, $arrayofselected)) $selected=1;
		print '<input id="cb'.$obj->id.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->id.'"'.($selected?' checked="checked"':'').'>';
    }
    print '</td>';
    if (! $i) $totalarray['nbfield']++;

	print '</tr>'."\n";
	$i++;
}

$db->free($resql);

print "</table>";
print "</div>";

print '</form>';
print '<script>$.applyDataMask();</script>';

llxFooter();
$db->close();
