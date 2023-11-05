<?php
/* Based on same file from Third Parties
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';

$langs->load("amhp");
$langs->load("amhpestimates");

llxHeader("",$langs->trans("AHMPEstimatesArea"));

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

// Security check
if (! $user->rights->amhpestimates->estimates->read) accessforbidden();

$search_all=trim(GETPOST('sall', 'alphanohtml'));
$search_cti=preg_replace('/^0+/', '', preg_replace('/[^0-9]/', '', GETPOST('search_cti', 'alphanohtml')));	// Phone number without any special chars

$search_ponumber=trim(GETPOST('search_ponumber'));
$search_customername=trim(GETPOST("search_customername"));
$search_phone=trim(GETPOST('search_phone'));
$search_town=trim(GETPOST("search_town"));
$search_state=trim(GETPOST("search_state"));
$search_zip=trim(GETPOST("search_zip"));
$search_email=trim(GETPOST('search_email'));

$mode=GETPOST("mode");
$diroutputmassaction=$conf->societe->dir_output . '/temp/massgeneration/'.$user->id;

$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$sortfield=GETPOST("sortfield",'alpha');
$sortorder=GETPOST("sortorder",'alpha');
$page=GETPOST("page",'int');
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="po.POID";
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	'tp.nom'=>"CustomerName",
	'tp.town'=>"City",
	'po.ponumber'=>"PO Number",
	'tp.phone'=>"Phone",
);

// Define list of fields to show into list
$arrayfields=array(
    'po.ponumber'=>array('label'=>"PONumber", 'checked'=>1),
    'tp.nom'=>array('label'=>"Customer", 'checked'=>1),
    'tp.phone'=>array('label'=>"Phone", 'checked'=>1),
    'tp.town'=>array('label'=>"Town", 'checked'=>1),
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
		$search_ponumber='';
		$search_customername='';
		$search_town="";
		$search_state="";
		$search_zip="";
		$search_email='';
		$search_phone='';
		$toselect='';
		$search_array_options=array();
	}

	// Mass actions
	$objectclass='EaProductionOrders';
	$objectlabel='Production Order';
	$permtoread = $user->rights->amhpestimates->estimates->read;
	$permtodelete = $user->rights->amhpestimates->estimates->update;
	$uploaddir = $conf->societe->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}

/*
 * View
 */

 if ($massaction == 'summarizepos')
{
    print '<ul><li>';
    print '<a href="print/posummary.php?tn=posummary_standard&ids[]='.implode('&ids[]=', array_map('urlencode', $toselect)).'" onclick="this.parentNode.remove();">Open Production Order Summary...</a>';
    print '</li></ul>';
}

$form=new Form($db);

$title=$langs->trans("AMHPESTIMATESListOfPOsPageHeading");

$sql = "SELECT po.poid, po.ponumber, po.podate, tp.rowid as socid, tp.nom as customername, tp.town, tp.zip, ";
$sql.= " tp.email, tp.phone,";
$sql.= " state.code_departement as state_code, state.nom as state_name";
$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = po.customerId)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as state on (state.rowid = tp.fk_departement)";
$sql.= " WHERE 1=1 ";

if ($search_all)           $sql.= natural_search(array_keys($fieldstosearchall), $search_all);
if (strlen($search_cti))   $sql.= natural_search('tp.phone', $search_cti);

if ($search_ponumber)      $sql.= natural_search("po.ponumber",$search_ponumber);
if ($search_customername)  $sql.= natural_search("tp.nom",$search_customername);
if ($search_town)          $sql.= natural_search("tp.town",$search_town);
if ($search_state)         $sql.= natural_search("state.nom",$search_state);
if (strlen($search_zip))   $sql.= natural_search("tp.zip",$search_zip);
if ($search_email)         $sql.= natural_search("tp.email",$search_email);
if (strlen($search_phone)) $sql.= natural_search("tp.phone", $search_phone);

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
    $id = $obj->poid;
    header("Location: ".DOL_URL_ROOT.'/custom/amhpestimates/card.php?poid='.$id);
    exit;
}

$param='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_all != '') $param = "&sall=".urlencode($search_all);
if ($sall != '') $param .= "&sall=".urlencode($sall);
if ($search_ponumber != '') $param.= "&search_ponumber=".urlencode($search_ponumber);
if ($search_customername != '') $param.= "&search_customername=".urlencode($search_customername);
if ($search_town != '') $param.= "&search_town=".urlencode($search_town);
if ($search_state != '') $param.= "&search_state=".urlencode($search_state);
if ($search_zip != '') $param.= "&search_zip=".urlencode($search_zip);
if ($search_email != '') $param.= "&search_email=".urlencode($search_email);
if ($search_phone != '') $param.= "&search_phone=".urlencode($search_phone);
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
if ($user->rights->amhpestimates->estimates->update) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($user->rights->amhpestimates->estimates->read) $arrayofmassactions['summarizepos']=$langs->trans("Production Orders Summary");
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
if (! empty($arrayfields['po.ponumber']['checked']))
{
    // PONUMBER
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_ponumber" value="'.dol_escape_htmltag($search_ponumber).'">';
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
	print '<input class="flat searchstring" size="4" type="text" name="search_phone" value="'.dol_escape_htmltag($search_phone).'">';
	print '</td>';
}
// Town
if (! empty($arrayfields['tp.town']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="6" type="text" name="search_town" value="'.dol_escape_htmltag($search_town).'">';
	print '</td>';
}
// Zip
if (! empty($arrayfields['tp.zip']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_zip" value="'.dol_escape_htmltag($search_zip).'">';
	print '</td>';
}
// State
if (! empty($arrayfields['state.nom']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_state" value="'.dol_escape_htmltag($search_state).'">';
	print '</td>';
}
if (! empty($arrayfields['tp.email']['checked']))
{
    // Email
	print '<td class="liste_titre">';
	print '<input class="flat searchemail" size="4" type="text" name="search_email" value="'.dol_escape_htmltag($search_email).'">';
	print '</td>';
}
// Action column
print '<td class="liste_titre" align="right">';
$searchpicto=$form->showFilterButtons();
print $searchpicto;
print '</td>';

print "</tr>\n";

print '<tr class="liste_titre">';
if (! empty($arrayfields['po.ponumber']['checked']))			print_liste_field_titre($arrayfields['po.ponumber']['label'],$_SERVER["PHP_SELF"],"po.ponumber","",$param,'',$sortfield,$sortorder);
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
	// PONUMBER
    if (! empty($arrayfields['po.ponumber']['checked']))
    {
        print '<td><a href="../../custom/amhpestimates/card.php?poid='.$obj->poid.'&mainmenu=amhpestimates"> '.$obj->ponumber.'</a></td>'."\n";
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
		if (in_array($obj->poid, $arrayofselected)) $selected=1;
		print '<input id="cb'.$obj->poid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->poid.'"'.($selected?' checked="checked"':'').'>';
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
