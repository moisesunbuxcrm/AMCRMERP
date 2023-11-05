<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php';

//$langs->load("admin");
//$langs->load("errors");
//$langs->load('other');
$langs->load('amhpestimates@amhpestimates');

if (! $user->admin) accessforbidden();

$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');
$label = GETPOST('label','alpha');
$scandir = GETPOST('scan_dir','alpha');
$type='amhppo';

/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

$error=0;
if ($action == 'specimen')
{
	$modele=GETPOST('module','alpha');

	$po = new EaProductionOrders($db);
	$po->initAsSpecimen();

	// Search template files
	$file=''; $classname=''; $filefound=0;
	$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);
	foreach($dirmodels as $reldir)
	{
		$file=dol_buildpath($reldir."core/modules/doc/pdf_".$modele.".modules.php");
		if (file_exists($file))
		{
			$filefound=1;
			$classname = "pdf_".$modele;
			break;
		}
	}

	if ($filefound)
	{
		require_once $file;

		$module = new $classname($db,$po);

		if ($module->write_file($langs) > 0)
		{
			header("Location: ".DOL_URL_ROOT."/document.php?modulepart=amhpestimates&file=SPECIMEN.pdf");
			return;
		}
		else
		{
			setEventMessages($module->error, $module->errors, 'errors');
			dol_syslog($module->error, LOG_ERR);
		}
	}
	else
	{
		setEventMessages($langs->trans("ErrorModuleNotFound"), null, 'errors');
		dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
	}
}

// Activate a model
if ($action == 'set')
{
	$ret = addDocumentModel($value, $type, $label, $scandir);
}

else if ($action == 'del')
{
	$ret = delDocumentModel($value, $type);
	if ($ret > 0)
	{
        if ($conf->global->AMHP_PO_TEMPLATE == "$value") dolibarr_del_const($db, 'AMHP_PO_TEMPLATE',$conf->entity);
	}
}

else if ($action == 'setdoc')
{
    if (dolibarr_set_const($db, "AMHP_PO_TEMPLATE",$value,'chaine',0,'',$conf->entity))
	{
		$conf->global->AMHP_PO_TEMPLATE = $value;
	}

	// On active le modele
	$ret = delDocumentModel($value, $type);
	if ($ret > 0)
	{
		$ret = addDocumentModel($value, $type, $label, $scandir);
	}
}

else if ($action == 'setmod')
{
	// TODO Verifier si module numerotation choisi peut etre active
	// par appel methode canBeActivated

	dolibarr_set_const($db, "PROPALE_ADDON",$value,'chaine',0,'',$conf->entity);
	error;
}

/*
 * View
 */

$form=new Form($db);

$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);

llxHeader('',$langs->trans("PropalSetup"));

//if ($mesg) print $mesg;

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("PropalSetup"),$linkback,'title_setup');

//$head = propal_admin_prepare_head();

//dol_fiche_head($head, 'general', $langs->trans("Proposals"), -1, 'propal');

/*
 * Document templates generators
 */

print load_fiche_titre($langs->trans("ProposalsPDFModules"),'','');

// Load array def with activated templates
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = '".$type."'";
$sql.= " AND entity = 1";
$resql=$db->query($sql);
if ($resql)
{
	$i = 0;
	$num_rows=$db->num_rows($resql);
	while ($i < $num_rows)
	{
		$array = $db->fetch_array($resql);
		array_push($def, $array[0]);
		$i++;
	}
}
else
{
	dol_print_error($db);
}

print "<table class=\"noborder\" width=\"100%\">\n";
print "<tr class=\"liste_titre\">\n";
print "  <td>".$langs->trans("Name")."</td>\n";
print "  <td>".$langs->trans("Description")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("Status")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("Default")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("ShortInfo").'</td>';
print '<td align="center" width="40">'.$langs->trans("Preview").'</td>';
print "</tr>\n";

clearstatcache();

$var=true;
foreach ($dirmodels as $reldir)
{
    foreach (array('','/doc') as $valdir)
    {
    	$dir = dol_buildpath($reldir."core/modules".$valdir);

        if (is_dir($dir))
        {
            $handle=opendir($dir);
            if (is_resource($handle))
            {
                while (($file = readdir($handle))!==false)
                {
                    $filelist[]=$file;
                }
                closedir($handle);
                arsort($filelist);

                foreach($filelist as $file)
                {
                    if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
                    {
                    	if (file_exists($dir.'/'.$file))
                    	{
                    		$name = substr($file, 4, dol_strlen($file) -16);
	                        $classname = substr($file, 0, dol_strlen($file) -12);

	                        require_once $dir.'/'.$file;
	                        $module = new $classname($db);

	                        $modulequalified=1;
	                        if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
	                        if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

	                        if ($modulequalified)
	                        {
	                            $var = !$var;
	                            print '<tr class="oddeven"><td width="100">';
	                            print (empty($module->name)?$name:$module->name);
	                            print "</td><td>\n";
	                            if (method_exists($module,'info')) print $module->info($langs);
	                            else print $module->description;
	                            print '</td>';

	                            // Active
	                            if (in_array($name, $def))
	                            {
	                            	print '<td align="center">'."\n";
	                            	print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&amp;value='.$name.'">';
	                            	print img_picto($langs->trans("Enabled"),'switch_on');
	                            	print '</a>';
	                            	print '</td>';
	                            }
	                            else
	                            {
	                                print "<td align=\"center\">\n";
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;value='.$name.'&amp;scan_dir='.$scandir.'&amp;label='.urlencode($module->name).'">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
	                                print "</td>";
	                            }

	                            // Defaut
	                            print "<td align=\"center\">";
	                            if ($conf->global->AMHP_PO_TEMPLATE == "$name")
	                            {
	                                print img_picto($langs->trans("Default"),'on');
	                            }
	                            else
	                            {
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&amp;value='.$name.'&amp;scan_dir='.$scandir.'&amp;label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	                            }
	                            print '</td>';

	                           // Info
	                            $htmltooltip =    ''.$langs->trans("Name").': '.$module->name;
	                            $htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
	                            if ($module->type == 'pdf')
	                            {
	                                $htmltooltip.='<br>'.$langs->trans("Width").'/'.$langs->trans("Height").': '.$module->page_width.'/'.$module->page_height;
	                            }
								$htmltooltip.='<br><br><u>'.$langs->trans("FeaturesSupported").':</u>';
								$htmltooltip.='<br>'.$langs->trans("Logo").': '.yn($module->option_logo,1,1);
								$htmltooltip.='<br>'.$langs->trans("MultiLanguage").': '.yn(1,1,1);
								$htmltooltip.='<br>'.$langs->trans("WatermarkOnDraftProposal").': '.yn(0,1,1);


	                            print '<td align="center">';
	                            print $form->textwithpicto('',$htmltooltip,1,0);
	                            print '</td>';

	                            // Preview
	                            print '<td align="center">';
	                            if ($module->type == 'pdf')
	                            {
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
	                            }
	                            else
	                            {
	                                print img_object($langs->trans("PreviewNotAvailable"),'generic');
	                            }
	                            print '</td>';

	                            print "</tr>\n";
	                        }
                    	}
                    }
                }
            }
        }
    }
}

print '</table>';


llxFooter();

$db->close();
