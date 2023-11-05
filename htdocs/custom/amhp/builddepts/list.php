<?php
/* Based on same file from Third Parties
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhp/class/eabuilddepts.class.php';

$langs->load("companies");

llxHeader("",$langs->trans("AHMPBuildDeptsArea"));

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

// Security check
if (! $user->rights->amhp->builddepts->read) accessforbidden();

$search_all=trim(GETPOST('sall', 'alphanohtml'));
$search_cti=preg_replace('/^0+/', '', preg_replace('/[^0-9]/', '', GETPOST('search_cti', 'alphanohtml')));	// Phone number without any special chars

$search_city_name=trim(GETPOST('search_city_name'));
$search_county=trim(GETPOST('search_county'));
$search_city_code=trim(GETPOST('search_city_code'));
$search_nom=trim(GETPOST("search_nom"));
$search_nom_only=trim(GETPOST("search_nom_only"));
$search_town=trim(GETPOST("search_town"));
$search_state=trim(GETPOST("search_state"));
$search_zip=trim(GETPOST("search_zip"));
$search_country=GETPOST("search_country",'intcomma');
$search_email=trim(GETPOST('search_email'));
$search_phone=trim(GETPOST('search_phone'));
$search_url=trim(GETPOST('search_url'));
$search_prop_search_url=trim(GETPOST('search_prop_search_url'));

$optioncss=GETPOST('optioncss','alpha');
$mode=GETPOST("mode");

$diroutputmassaction=$conf->societe->dir_output . '/temp/massgeneration/'.$user->id;

$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$sortfield=GETPOST("sortfield",'alpha');
$sortorder=GETPOST("sortorder",'alpha');
$page=GETPOST("page",'int');
if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="s.city_name";
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	's.city_name'=>"CityName",
	's.county'=>"County",
	's.city_code'=>"CityCode",
	's.nom'=>"BuildingDepartmentName",
);

// Define list of fields to show into list
$arrayfields=array(
    's.city_name'=>array('label'=>"AMHPCityName", 'checked'=>1),
    's.county'=>array('label'=>"AMHPCounty", 'checked'=>1),
    's.city_code'=>array('label'=>"AMHPCityCode", 'checked'=>1),
    's.nom'=>array('label'=>"AMHPBuildingDepartmentName", 'checked'=>1),
    's.town'=>array('label'=>"Town", 'checked'=>0),
    'state.nom'=>array('label'=>"State", 'checked'=>0),
    's.zip'=>array('label'=>"Zip", 'checked'=>0),
	'country.code_iso'=>array('label'=>"Country", 'checked'=>0),
    's.email'=>array('label'=>"Email", 'checked'=>0),
    's.phone'=>array('label'=>"Phone", 'checked'=>0),
    's.url'=>array('label'=>"Url", 'checked'=>0),
    's.prop_search_url'=>array('label'=>"AMHPPropSearch", 'checked'=>0),
);
$object = new Eabuilddepts($db);

/*
 * Actions
 */

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Did we click on purge search criteria ?
	if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') || GETPOST('button_removefilter','alpha')) // All tests are required to be compatible with all browsers
	{
		$search_city_name='';
		$search_county='';
		$search_city_code='';
		$search_nom='';
		$search_town="";
		$search_state="";
		$search_zip="";
		$search_country='';
		$search_email='';
		$search_phone='';
		$search_url='';
		$search_prop_search_url='';
		$toselect='';
		$search_array_options=array();
	}

	// Mass actions
	$objectclass='Eabuilddepts';
	$objectlabel='Building Department';
	$permtoread = $user->rights->amhp->builddepts->read;
	$permtodelete = $user->rights->amhp->builddepts->update;
	$uploaddir = $conf->societe->dir_output;
	include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}

/*
 * View
 */

$form=new Form($db);
$buildingdept=new Eabuilddepts($db);

$title=$langs->trans("AMHPListOfBuildingDepartmentsPageHeading");

$sql = "SELECT s.rowid, s.nom as name, s.town, s.zip, s.datec, s.code_client, ";
$sql.= " s.email, s.phone, s.url, s.fk_pays,";
$sql.= " s.tms as date_update, s.datec as date_creation,";
$sql.= " state.code_departement as state_code, state.nom as state_name,";
$sql.= " s.city_name, s.county, s.city_code, s.prop_search_url";
$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as country on (country.rowid = s.fk_pays)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as state on (state.rowid = s.fk_departement)";
$sql.= " WHERE 1=1 ";

if ($search_all)           $sql.= natural_search(array_keys($fieldstosearchall), $search_all);
if (strlen($search_cti))   $sql.= natural_search('s.phone', $search_cti);

if ($search_city_name)     $sql.= natural_search("s.city_name",$search_city_name);
if ($search_county)        $sql.= natural_search("s.county",$search_county);
if ($search_city_code)     $sql.= natural_search("s.city_code",$search_city_code);
if ($search_nom)           $sql.= natural_search("s.nom",$search_nom);
if ($search_nom_only)      $sql.= natural_search("s.nom",$search_nom_only);
if ($search_town)          $sql.= natural_search("s.town",$search_town);
if ($search_state)         $sql.= natural_search("state.nom",$search_state);
if (strlen($search_zip))   $sql.= natural_search("s.zip",$search_zip);
if ($search_country)       $sql .= " AND s.fk_pays IN (".$search_country.')';
if ($search_email)         $sql.= natural_search("s.email",$search_email);
if (strlen($search_phone)) $sql.= natural_search("s.phone", $search_phone);
if ($search_url)           $sql.= natural_search("s.url",$search_url);
if ($search_prop_search_url)           $sql.= natural_search("s.prop_search_url",$search_prop_search_url);

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
    $id = $obj->rowid;
    header("Location: ".DOL_URL_ROOT.'/custom/amhp/card.php?socid='.$id);
    exit;
}

$param='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_all != '') $param = "&sall=".urlencode($search_all);
if ($sall != '') $param .= "&sall=".urlencode($sall);
if ($search_city_name != '') $param.= "&search_city_name=".urlencode($search_city_name);
if ($search_county != '') $param.= "&search_ccounty=".urlencode($search_county);
if ($search_city_code != '') $param.= "&search_city_code=".urlencode($search_city_code);
if ($search_nom != '') $param.= "&search_nom=".urlencode($search_nom);
if ($search_town != '') $param.= "&search_town=".urlencode($search_town);
if ($search_state != '') $param.= "&search_state=".urlencode($search_state);
if ($search_zip != '') $param.= "&search_zip=".urlencode($search_zip);
if ($search_country != '') $param.='&search_country='.urlencode($search_country);
if ($search_email != '') $param.= "&search_email=".urlencode($search_email);
if ($search_phone != '') $param.= "&search_phone=".urlencode($search_phone);
if ($search_url != '') $param.= "&search_url=".urlencode($search_url);
if ($search_prop_search_url != '') $param.= "&search_prop_search_url=".urlencode($search_prop_search_url);
if ($optioncss != '') $param.='&optioncss='.urlencode($optioncss);
// Show delete result message
if (GETPOST('delsoc'))
{
    setEventMessages($langs->trans("AMHPBuildingDepartmentDeleted",GETPOST('delsoc')), null, 'mesgs');
}

// List of mass actions available
$arrayofmassactions =  array(
//    'presend'=>$langs->trans("SendByMail"),
//    'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->amhp->builddepts->update) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" name="formfilter">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
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
if (! empty($arrayfields['s.city_name']['checked']))
{
    // City Name
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_city_name" value="'.dol_escape_htmltag($search_city_name).'">';
	print '</td>';
}
if (! empty($arrayfields['s.county']['checked']))
{
    // County
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_county" value="'.dol_escape_htmltag($search_county).'">';
	print '</td>';
}
if (! empty($arrayfields['s.city_code']['checked']))
{
    // City Code
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_city_code" value="'.dol_escape_htmltag($search_city_code).'">';
	print '</td>';
}
if (! empty($arrayfields['s.nom']['checked']))
{
	print '<td class="liste_titre">';
	if (! empty($search_nom_only) && empty($search_nom)) $search_nom=$search_nom_only;
	print '<input class="flat searchstring" type="text" name="search_nom" size="8" value="'.dol_escape_htmltag($search_nom).'">';
	print '</td>';
}
// Town
if (! empty($arrayfields['s.town']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="6" type="text" name="search_town" value="'.dol_escape_htmltag($search_town).'">';
	print '</td>';
}
// State
if (! empty($arrayfields['state.nom']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_state" value="'.dol_escape_htmltag($search_state).'">';
	print '</td>';
}
// Zip
if (! empty($arrayfields['s.zip']['checked']))
{
    print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_zip" value="'.dol_escape_htmltag($search_zip).'">';
	print '</td>';
}
// Country
if (! empty($arrayfields['country.code_iso']['checked']))
{
    print '<td class="liste_titre" align="center">';
	print $form->select_country($search_country,'search_country','',0,'maxwidth100');
	print '</td>';
}
if (! empty($arrayfields['s.email']['checked']))
{
    // Email
	print '<td class="liste_titre">';
	print '<input class="flat searchemail" size="4" type="text" name="search_email" value="'.dol_escape_htmltag($search_email).'">';
	print '</td>';
}
if (! empty($arrayfields['s.phone']['checked']))
{
    // Phone
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_phone" value="'.dol_escape_htmltag($search_phone).'">';
	print '</td>';
}
if (! empty($arrayfields['s.url']['checked']))
{
    // Url
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_url" value="'.dol_escape_htmltag($search_url).'">';
	print '</td>';
}
if (! empty($arrayfields['s.prop_search_url']['checked']))
{
    // PropSearchUrl
	print '<td class="liste_titre">';
	print '<input class="flat searchstring" size="4" type="text" name="search_prop_search_url" value="'.dol_escape_htmltag($search_prop_search_url).'">';
	print '</td>';
}
// Action column
print '<td class="liste_titre" align="right">';
$searchpicto=$form->showFilterButtons();
print $searchpicto;
print '</td>';

print "</tr>\n";

print '<tr class="liste_titre">';
if (! empty($arrayfields['s.city_name']['checked']))		print_liste_field_titre($arrayfields['s.city_name']['label'],$_SERVER["PHP_SELF"],"s.city_name","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.county']['checked']))			print_liste_field_titre($arrayfields['s.county']['label'],$_SERVER["PHP_SELF"],"s.county","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.city_code']['checked']))		print_liste_field_titre($arrayfields['s.city_code']['label'],$_SERVER["PHP_SELF"],"s.city_code","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.nom']['checked']))				print_liste_field_titre($arrayfields['s.nom']['label'], $_SERVER["PHP_SELF"],"s.nom","",$param,"",$sortfield,$sortorder);
if (! empty($arrayfields['s.town']['checked']))				print_liste_field_titre($arrayfields['s.town']['label'],$_SERVER["PHP_SELF"],"s.town","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['state.nom']['checked']))			print_liste_field_titre($arrayfields['state.nom']['label'],$_SERVER["PHP_SELF"],"state.nom","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.zip']['checked']))				print_liste_field_titre($arrayfields['s.zip']['label'],$_SERVER["PHP_SELF"],"s.zip","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['country.code_iso']['checked']))	print_liste_field_titre($arrayfields['country.code_iso']['label'],$_SERVER["PHP_SELF"],"country.code_iso","",$param,'align="center"',$sortfield,$sortorder);
if (! empty($arrayfields['s.email']['checked']))			print_liste_field_titre($arrayfields['s.email']['label'],$_SERVER["PHP_SELF"],"s.email","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.phone']['checked']))			print_liste_field_titre($arrayfields['s.phone']['label'],$_SERVER["PHP_SELF"],"s.phone","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.url']['checked']))				print_liste_field_titre($arrayfields['s.url']['label'],$_SERVER["PHP_SELF"],"s.url","",$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['s.prop_search_url']['checked']))	print_liste_field_titre($arrayfields['s.prop_search_url']['label'],$_SERVER["PHP_SELF"],"s.prop_search_url","",$param,'',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="center"',$sortfield,$sortorder,'maxwidthsearch ');
print "</tr>\n";


$i = 0;
$totalarray=array();
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);

	$buildingdept->id=$obj->rowid;
	$buildingdept->name=$obj->name;
	$buildingdept->code_client=$obj->code_client;
	$buildingdept->city_name=$obj->city_name;

	print '<tr class="oddeven">';
	// City Name
    if (! empty($arrayfields['s.city_name']['checked']))
    {
		print '<td class="tdoverflowmax200">';
		print $buildingdept->getCityNameUrl();
        print "</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
	// County
    if (! empty($arrayfields['s.county']['checked']))
    {
        print "<td>".$obj->county."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
	// City Code
    if (! empty($arrayfields['s.city_code']['checked']))
    {
        print "<td>".$obj->city_code."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
	if (! empty($arrayfields['s.nom']['checked']))
	{
		print '<td class="tdoverflowmax200">';
		print $obj->name;
		print "</td>\n";
        if (! $i) $totalarray['nbfield']++;
	}
	// Town
    if (! empty($arrayfields['s.town']['checked']))
    {
        print "<td>".$obj->town."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    // State
    if (! empty($arrayfields['state.nom']['checked']))
    {
        print "<td>".$obj->state_name."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    // Zip
    if (! empty($arrayfields['s.zip']['checked']))
    {
        print "<td>".$obj->zip."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    // Country
    if (! empty($arrayfields['country.code_iso']['checked']))
    {
        print '<td align="center">';
		$tmparray=getCountry($obj->fk_pays,'all');
		print $tmparray['label'];
		print '</td>';
        if (! $i) $totalarray['nbfield']++;
    }
    if (! empty($arrayfields['s.email']['checked']))
    {
        print "<td>".$obj->email."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    if (! empty($arrayfields['s.phone']['checked']))
    {
        print "<td>".$obj->phone."</td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    if (! empty($arrayfields['s.url']['checked']))
    {
        print "<td><a href='".$obj->url."'>".$obj->url."</a></td>\n";
        if (! $i) $totalarray['nbfield']++;
    }
    if (! empty($arrayfields['s.prop_search_url']['checked']))
    {
        print "<td><a href='".$obj->prop_search_url."'>".$obj->prop_search_url."</a></td>\n";
        if (! $i) $totalarray['nbfield']++;
    }

    // Action column
    print '<td class="nowrap" align="center">';
    if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
    {
        $selected=0;
		if (in_array($obj->rowid, $arrayofselected)) $selected=1;
		print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
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

llxFooter();
$db->close();
