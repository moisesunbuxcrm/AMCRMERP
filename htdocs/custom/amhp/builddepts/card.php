<?php
/* Based on same file from Third Parties
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhp/class/eabuilddepts.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

$langs->load("companies");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','aZ09') ? GETPOST('action','aZ09') : 'view');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage','alpha');
$confirm	= GETPOST('confirm');
$socid		= GETPOST('socid','int');
if (empty($socid) && $action == 'view') $action='create';

$object = new Eabuilddepts($db);

if ($action == 'view' && $object->fetch($socid)<=0)
{
	$langs->load("errors");
	print $object->error.'<br>';
	print($langs->trans('ErrorRecordNotFound'));
	exit;
}

/*
 * Actions
 */

{
    if ($cancel)
    {
        $action='';
        if (! empty($backtopage))
        {
            header("Location: ".$backtopage);
            exit;
        }
    }

    // Add new or update building department
    if (($action == 'add' || $action == 'update') && $user->rights->amhp->builddepts->update)
    {
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

        if ($action == 'update')
        {
        	$ret=$object->fetch($socid);
			$object->oldcopy = clone $object;
        }

		$object->name              	   = GETPOST('name', 'alpha');
        $object->address               = GETPOST('address');
        $object->zip                   = GETPOST('zipcode', 'alpha');
        $object->town                  = GETPOST('town', 'alpha');
        $object->state_id              = GETPOST('state_id', 'int');
        $object->phone                 = GETPOST('phone', 'alpha');
        $object->fax                   = GETPOST('fax','alpha');
        $object->email                 = GETPOST('email', 'custom', 0, FILTER_SANITIZE_EMAIL);
        $object->url                   = GETPOST('url', 'custom', 0, FILTER_SANITIZE_URL);

        $object->default_lang          = GETPOST('default_lang');

		$object->city_code             = GETPOST('city_code', 'alpha');
		$object->city_name             = GETPOST('city_name', 'alpha');
		$object->working_hours         = GETPOST('working_hours', 'alpha');
		$object->county                = GETPOST('county', 'alpha');
        $object->prop_search_url       = GETPOST('prop_search_url', 'custom', 0, FILTER_SANITIZE_URL);
		
        // Check parameters
        if (! GETPOST("cancel"))
        {
            if (! empty($object->email) && ! isValidEMail($object->email))
            {
                $langs->load("errors");
                $error++; $errors[] = $langs->trans("ErrorBadEMail",$object->email);
                $action = ($action=='add'?'create':'edit');
            }
            if (! empty($object->url) && ! isValidUrl($object->url))
            {
                $langs->load("errors");
                $error++; $errors[] = $langs->trans("ErrorBadUrl",$object->url);
                $action = ($action=='add'?'create':'edit');
            }

            // We set country_id, country_code and country for the selected country
            $object->country_id=GETPOST('country_id')!=''?GETPOST('country_id'):11;
            if ($object->country_id)
            {
            	$tmparray=getCountry($object->country_id,'all');
            	$object->country_code=$tmparray['code'];
            	//$object->country=$tmparray['label'];
            }

        }

        if (! $error)
        {
            if ($action == 'add')
            {
                $db->begin();

                $result = $object->create($user);

				if ($result >= 0)
                {
                }
                else
				{
				    if ($db->lasterrno() == 'DB_ERROR_RECORD_ALREADY_EXISTS')
					{
						$duplicate_code_error = true;
					}

                    $error=$object->error; $errors=$object->errors;
                }

                if ($result >= 0)
                {
                    $db->commit();

                	if (! empty($backtopage))
                	{
                	    if (preg_match('/\?/', $backtopage)) $backtopage.='&socid='.$object->id;
               		    header("Location: ".$backtopage);
                    	exit;
                	}
                	else
                	{
                    	$url=$_SERVER["PHP_SELF"]."?socid=".$object->id;
                		header("Location: ".$url);
                    	exit;
                	}
                }
                else
                {
                    $db->rollback();
                    $action='create';
                }
            }

            if ($action == 'update')
            {
                if (GETPOST("cancel"))
                {
                	if (! empty($backtopage))
                	{
               		    header("Location: ".$backtopage);
                    	exit;
                	}
                	else
                	{
               		    header("Location: ".$_SERVER["PHP_SELF"]."?socid=".$socid);
                    	exit;
                	}
                }

                $result = $object->update($user);
                if ($result <=  0)
                {
                    $error = $object->error; $errors = $object->errors;
                }

                if (! $error && ! count($errors))
                {
                    if (! empty($backtopage))
                	{
               		    header("Location: ".$backtopage);
                    	exit;
                	}
                	else
                	{
               		    header("Location: ".$_SERVER["PHP_SELF"]."?socid=".$socid);
                    	exit;
                	}
                }
                else
                {
                    $object->id = $socid;
                    $action= "edit";
                }
            }
        }
    }

    // Delete building department
    if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->amhp->builddepts->update)
    {
        $object->fetch($socid);
        $result = $object->delete($user);

        if ($result > 0)
        {
            header("Location: ".DOL_URL_ROOT."/custom/amhp/builddepts/list.php?delsoc=".urlencode($object->name));
            exit;
        }
        else
        {
            $langs->load("errors");
            $error=$langs->trans($object->error); $errors = $object->errors;
            $action='';
        }
    }

    // Actions to send emails
    $id=$socid;
    $trigger_name='COMPANY_SENTBYMAIL';
    $paramname='socid';
    $mode='emailfromthirdparty';
    $trackid='thi'.$object->id;
    include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';

    // Actions to build doc
    $id = $socid;
    $upload_dir = '/amhp';
    $permissioncreate=$user->rights->amhp->builddepts->update;
    include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';
}



/*
 *  View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formadmin = new FormAdmin($db);
$formcompany = new FormCompany($db);

if ($socid > 0 && empty($object->id))
{
    $result=$object->fetch($socid);
	if ($result <= 0) dol_print_error('',$object->error);
}

$title=$langs->trans("AMHPBuildingDepartmentPageTitle");
llxHeader('',$title);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

{
    // -----------------------------------------
    // When used in standard mode
    // -----------------------------------------
    if ($action == 'create')
    {
        /*
         *  Creation
         */
        $object->name				= GETPOST('name', 'alpha');

        $object->address			= GETPOST('address', 'alpha');
        $object->zip				= GETPOST('zipcode', 'alpha');
        $object->town				= GETPOST('town', 'alpha');
        $object->state_id			= GETPOST('state_id', 'int');
        $object->country_id         = GETPOST('country_id', 'int');
        $object->phone				= GETPOST('phone', 'alpha');
        $object->fax				= GETPOST('fax', 'alpha');
        $object->email				= GETPOST('email', 'custom', 0, FILTER_SANITIZE_EMAIL);
        $object->url				= GETPOST('url', 'custom', 0, FILTER_SANITIZE_URL);

        $object->default_lang		= GETPOST('default_lang');

        $object->city_code          = GETPOST('city_code', 'alpha');
        $object->city_name          = GETPOST('city_name', 'alpha');
        $object->working_hours      = GETPOST('working_hours', 'alpha');
        $object->county             = GETPOST('county', 'alpha');
        $object->prop_search_url    = GETPOST('prop_search_url', 'custom', 0, FILTER_SANITIZE_URL);
		
        // We set country_id, country_code and country for the selected country
        $object->country_id=GETPOST('country_id')?GETPOST('country_id'):11;
        if ($object->country_id)
        {
            $tmparray=getCountry($object->country_id,'all');
            $object->country_code=$tmparray['code'];
            //$object->country=$tmparray['label'];
        }
		
        /* Show create form */

        $linkback="";
        print load_fiche_titre($langs->trans("AMHPNewBuildingDepartmentPageHeading"),$linkback,'title_companies.png');

        if (! empty($conf->use_javascript_ajax))
        {
            print "\n".'<script type="text/javascript">';
            print '$(document).ready(function () {
                        $("#selectcountry_id").change(function() {
                        	document.formsoc.action.value="create";
                        	document.formsoc.submit();
                        });
                     });';
            print '</script>'."\n";
		}
		
        dol_htmloutput_mesg(is_numeric($error)?'':$error, $errors, 'error');

        print '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="post" name="formsoc">';

        print '<input type="hidden" name="action" value="add">';
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="LastName" value="'.$langs->trans('AMHPBuildingDepartmentName').' / '.$langs->trans('LastName').'">';
        print '<input type="hidden" name="ThirdPartyName" value="'.$langs->trans('AMHPBuildingDepartmentName').'">';

        dol_fiche_head(null, 'card', '', 0, '');

        print '<table class="border" width="100%">';

        // Name
	    print '<tr><td class="titlefieldcreate">';
		print '<span id="TypeName">'.fieldLabel('AMHPBuildingDepartmentName','name').'</span>';
		print '</td><td>';
	    print '<input type="text" class="minwidth300" maxlength="128" name="name" id="name" value="'.$object->name.'" autofocus="autofocus"></td>';
	    print '</tr>';

        // Address
        print '<tr><td class="tdtop">'.fieldLabel('Address','address').'</td>';
	    print '<td colspan="3"><textarea name="address" id="address" class="quatrevingtpercent" rows="'._ROWS_2.'" wrap="soft">';
        print $object->address;
        print '</textarea></td></tr>';

        // Zip / Town
        print '<tr><td>'.fieldLabel('Zip','zipcode').'</td><td>';
        print $formcompany->select_ziptown($object->zip,'zipcode',array('town','selectcountry_id','state_id'), 0, 0, '', 'maxwidth300 quatrevingtpercent');
        print '</td><td>'.fieldLabel('Town','town').'</td><td>';
        print $formcompany->select_ziptown($object->town,'town',array('zipcode','selectcountry_id','state_id'), 0, 0, '', 'maxwidth300 quatrevingtpercent');
        print '</td></tr>';

		// City Code / Name
        print '<tr><td>'.fieldLabel('AMHPCityCode','city_code').'</td>';
	    print '<td><input type="text" class="minwidth300" name="city_code" id="city_code" value="'.$object->city_code.'"></td>';
        print '<td>'.fieldLabel('AMHPCityName','city_name').'</td>';
	    print '<td><input type="text" class="minwidth300" name="city_name" id="city_name" value="'.$object->city_name.'"></td>';
        print '</tr>';

        // Country
        print '<tr><td>'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
        print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):($object->country_id!=''?$object->country_id:11)),'country_id');
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';

        // State
		print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
		print $formcompany->select_state(($object->state_id!=''?$object->state_id:801),$object->country_code);
		print '</td></tr>';

		// county & property search url
        print '<tr><td>'.fieldLabel('AMHPCounty','county').'</td>';
	    print '<td><input type="text" class="minwidth300" name="county" id="county" value="'.$object->county.'"></td>';
        print '<td>'.fieldLabel('AMHPPropSearch','prop_search_url').'</td>';
	    print '<td><input type="text" class="minwidth300" name="prop_search_url" id="prop_search_url" value="'.$object->prop_search_url.'"></td>';
        print '</tr>';
		
        // Email web
        print '<tr><td>'.fieldLabel('EMail','email').'</td>';
	    print '<td colspan="3"><input type="text" name="email" id="email" value="'.$object->email.'"></td></tr>';
        print '<tr><td>'.fieldLabel('Web','url').'</td>';
	    print '<td colspan="3"><input type="text" name="url" id="url" value="'.$object->url.'"></td></tr>';

        // Phone / Fax
        print '<tr><td>'.fieldLabel('Phone','phone').'</td>';
	    print '<td><input type="text" name="phone" id="phone" class="maxwidth100onsmartphone quatrevingtpercent" value="'.$object->phone.'"></td>';
        print '<td>'.fieldLabel('Fax','fax').'</td>';
	    print '<td><input type="text" name="fax" id="fax" class="maxwidth100onsmartphone quatrevingtpercent" value="'.$object->fax.'"></td></tr>';

        // Working Hours
        print '<tr><td class="tdtop">'.fieldLabel('AMHPWorkingHours','working_hours').'</td>';
	    print '<td colspan="3"><textarea name="working_hours" id="working_hours" class="quatrevingtpercent" rows="'._ROWS_2.'" wrap="soft">';
        print $object->working_hours;
        print '</textarea></td></tr>';
		
        print '</table>'."\n";

        dol_fiche_end();

        print '<div class="center">';
        print '<input type="submit" class="button" name="create" value="'.$langs->trans('AMHPAddBuildingDepartment').'">';
        if (! empty($backtopage))
        {
            print ' &nbsp; &nbsp; ';
            print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
        }
        else
        {
            print ' &nbsp; &nbsp; ';
            print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
        }
        print '</div>'."\n";

        print '</form>'."\n";
    }
    elseif ($action == 'edit')
    {
        /*
         * Edition
         */

        if ($socid)
        {
	        $head = $object->prepare_head();

			$object->oldcopy = clone $object;

            if (GETPOST('name'))
            {
                // We overwrite with values if posted
                $object->name					= GETPOST('name', 'alpha');
                $object->address				= GETPOST('address', 'alpha');
                $object->zip					= GETPOST('zipcode', 'alpha');
                $object->town					= GETPOST('town', 'alpha');
                $object->state_id				= GETPOST('state_id', 'int')?GETPOST('state_id', 'int'):801;
                $object->country_id				= GETPOST('country_id', 'int')?GETPOST('country_id', 'int'):11;
                $object->phone					= GETPOST('phone', 'alpha');
                $object->fax					= GETPOST('fax', 'alpha');
                $object->email					= GETPOST('email', 'custom', 0, FILTER_SANITIZE_EMAIL);
                $object->url					= GETPOST('url', 'custom', 0, FILTER_SANITIZE_URL);
                $object->default_lang			= GETPOST('default_lang', 'alpha');

				$object->city_code          = GETPOST('city_code', 'alpha');
				$object->city_name          = GETPOST('city_name', 'alpha');
				$object->working_hours      = GETPOST('working_hours', 'alpha');
				$object->county             = GETPOST('county', 'alpha');
				$object->prop_search_url    = GETPOST('prop_search_url', 'custom', 0, FILTER_SANITIZE_URL);

                // We set country_id, and country_code label of the chosen country
                if ($object->country_id > 0)
                {
                	$tmparray=getCountry($object->country_id,'all');
                    $object->country_code	= $tmparray['code'];
                    //$object->country		= $tmparray['label'];
                }
            }

            dol_htmloutput_errors($error,$errors);

            if ($conf->use_javascript_ajax)
            {
                print "\n".'<script type="text/javascript" language="javascript">';
                print '$(document).ready(function () {
                			$("#selectcountry_id").change(function() {
                				document.formsoc.action.value="edit";
                				document.formsoc.submit();
                			});
                       })';
                print '</script>'."\n";
            }

            print '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?socid='.$object->id.'" method="post" name="formsoc">';
            print '<input type="hidden" name="action" value="update">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="socid" value="'.$object->id.'">';

			dol_fiche_head($head, 'card', $langs->trans("AMHPEditBuildingDepartmentPageHeading"), 0, 'company');

            print '<div class="fichecenter2">';
            print '<table class="border" width="100%">';

            // Ref/ID
			if (! empty($conf->global->MAIN_SHOW_TECHNICAL_ID))
			{
		        print '<tr><td class="titlefieldcreate">'.$langs->trans("ID").'</td><td colspan="3">';
            	print $object->ref;
            	print '</td></tr>';
			}

            // Name
            print '<tr><td class="titlefieldcreate">'.fieldLabel('AMHPBuildingDepartmentName','name',0).'</td>';
	        print '<td colspan="3"><input type="text" class="minwidth300" maxlength="128" name="name" id="name" value="'.dol_escape_htmltag($object->name).'" autofocus="autofocus"></td></tr>';

            // Address
            print '<tr><td class="tdtop">'.fieldLabel('Address','address').'</td>';
	        print '<td colspan="3"><textarea name="address" id="address" class="quatrevingtpercent" rows="3" wrap="soft">';
            print $object->address;
            print '</textarea></td></tr>';

            // Zip / Town
            print '<tr><td>'.fieldLabel('Zip','zipcode').'</td><td>';
            print $formcompany->select_ziptown($object->zip, 'zipcode', array('town', 'selectcountry_id', 'state_id'), 0, 0, '', 'maxwidth50onsmartphone');
            print '</td><td>'.fieldLabel('Town','town').'</td><td>';
            print $formcompany->select_ziptown($object->town, 'town', array('zipcode', 'selectcountry_id', 'state_id'));
            print '</td></tr>';

			// City Code / Name
			print '<tr><td>'.fieldLabel('AMHPCityCode','city_code').'</td>';
			print '<td><input type="text" class="minwidth300" name="city_code" id="city_code" value="'.$object->city_code.'"></td>';
			print '<td>'.fieldLabel('AMHPCityName','city_name').'</td>';
			print '<td><input type="text" class="minwidth300" name="city_name" id="city_name" value="'.$object->city_name.'"></td>';
			print '</tr>';
			
            // Country
            print '<tr><td>'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3">';
            print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->country_id),'country_id');
            if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
            print '</td></tr>';

            // State
			print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3">';
		print "<!-- state_id=$object->state_id, country_code=$object->country_code -->";
			print $formcompany->select_state($object->state_id,$object->country_code);
			print '</td></tr>';

			// county & property search url
			print '<tr><td>'.fieldLabel('AMHPCounty','county').'</td>';
			print '<td><input type="text" class="minwidth300" name="county" id="county" value="'.$object->county.'"></td>';
			print '<td>'.fieldLabel('AMHPPropSearch','prop_search_url').'</td>';
			print '<td><input type="text" class="minwidth300" name="prop_search_url" id="prop_search_url" value="'.$object->prop_search_url.'"></td>';
			print '</tr>';

			// EMail / Web
            print '<tr><td>'.fieldLabel('EMail','email',0).'</td>';
	        print '<td colspan="3"><input type="text" name="email" id="email" size="32" value="'.$object->email.'"></td></tr>';
            print '<tr><td>'.fieldLabel('Web','url').'</td>';
	        print '<td colspan="3"><input type="text" name="url" id="url" size="32" value="'.$object->url.'"></td></tr>';

            // Phone / Fax
            print '<tr><td>'.fieldLabel('Phone','phone').'</td>';
	        print '<td><input type="text" name="phone" id="phone" class="maxwidth100onsmartphone quatrevingtpercent" value="'.$object->phone.'"></td>';
            print '<td>'.fieldLabel('Fax','fax').'</td>';
	        print '<td><input type="text" name="fax" id="fax" class="maxwidth100onsmartphone quatrevingtpercent" value="'.$object->fax.'"></td></tr>';

			// Working Hours
			print '<tr><td class="tdtop">'.fieldLabel('AMHPWorkingHours','working_hours').'</td>';
			print '<td colspan="3"><textarea name="working_hours" id="working_hours" class="quatrevingtpercent" rows="'._ROWS_2.'" wrap="soft">';
			print $object->working_hours;
			print '</textarea></td></tr>';

            print '</table>';
            print '</div>';

	        dol_fiche_end();

            print '<div align="center">';
            print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
            print ' &nbsp; &nbsp; ';
            print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
            print '</div>';

            print '</form>';
        }
    }
    else
    {
        /*
         * View
         */

        $head = $object->prepare_head();

        dol_fiche_head($head, 'card', $langs->trans("AMHPViewBuildingDepartmentPageHeading"), -1, 'company');

        // Confirm delete third party
        if ($action == 'delete' || ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile)))
        {
            print $form->formconfirm($_SERVER["PHP_SELF"]."?socid=".$object->id, $langs->trans("AMHPDeleteBuildingDepartment"), $langs->trans("AMHPConfirmDeleteBuildingDepartment"), "confirm_delete", '', 0, "action-delete");
        }

        dol_htmloutput_errors($error,$errors);

        $linkback = '<a href="'.DOL_URL_ROOT.'/custom/amhp/builddepts/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

		$old_element = $object->element;
        dol_banner_tab($object, 'socid', $linkback, 1, 'rowid', 'nom');
		$object->element = $old_element;


        print '<div class="fichecenter">';
        print '<div class="fichehalfleft">';

        print '<div class="underbanner clearboth"></div>';
        print '<table class="border tableforfield" width="100%">';

		// Address
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Address').'</td>';
    	print '<td>'.$object->address.'</td>';
    	print '</tr>';
		
		// Country
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Country').'</td>';
    	print '<td>'.$object->country_code.'</td>';
    	print '</tr>';

		// State
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('State').'</td>';
    	print '<td>'.$object->state_name.'</td>';
    	print '</tr>';
				
 		// Zip
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Zip').'</td>';
    	print '<td>'.$object->zip.'</td>';
    	print '</tr>';
		
 		// Email
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('EMail').'</td>';
    	print '<td>'.$object->email.'</td>';
    	print '</tr>';
		
 		// Phone
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Phone').'</td>';
    	print '<td>'.$object->phone.'</td>';
    	print '</tr>';
		
 		// Fax
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Fax').'</td>';
    	print '<td>'.$object->fax.'</td>';
    	print '</tr>';
		
       print '</table>';

        print '</div>';
        print '<div class="fichehalfright"><div class="ficheaddleft">';

        print '<div class="underbanner clearboth"></div>';
        print '<table class="border tableforfield" width="100%">';

		// Name
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('AMHPBuildingDepartmentName').'</td>';
    	print '<td>'.$object->name.'</td>';
    	print '</tr>';

 		// City Code
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('AMHPCityCode').'</td>';
    	print '<td>'.$object->city_code.'</td>';
    	print '</tr>';

 		// City Name
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('AMHPCityName').'</td>';
    	print '<td>'.$object->city_name.'</td>';
    	print '</tr>';
		
 		// Town
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Town').'</td>';
    	print '<td>'.$object->town.'</td>';
    	print '</tr>';
		
 		// County
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('AMHPCounty').'</td>';
    	print '<td>'.$object->county.'</td>';
    	print '</tr>';
		
 		// Url
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('Web').'</td>';
        print "<td><a target='_blank' href='".$object->url."'>".$object->url."</a></td>\n";
    	print '</tr>';
		
 		// PropertySearchUrl
    	print '<tr>';
		print '<td class="titlefield">'.$langs->trans('AMHPPropSearch').'</td>';
        print "<td><a target='_blank' href='".$object->prop_search_url."'>".$object->prop_search_url."</a></td>\n";
    	print '</tr>';
		
        print '</table>';
		print '</div>';

        print '</div></div>';
        print '<div style="clear:both"></div>';

        dol_fiche_end();


        /*
         *  Actions
         */
        print '<div class="tabsAction">'."\n";

		{
	        if (! empty($object->email))
	        {
	        	$langs->load("mails");
	        	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?socid='.$object->id.'&amp;action=presend&amp;mode=init">'.$langs->trans('SendMail').'</a></div>';
	        }
	        else
			{
	        	$langs->load("mails");
	       		print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NoEMail")).'">'.$langs->trans('SendMail').'</a></div>';
	        }

	        if ($user->rights->amhp->builddepts->update)
	        {
	            print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
	        }

	        if ($user->rights->amhp->builddepts->update)
	        {
	            if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
	            {
	                print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
	            }
	            else
				{
	                print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?socid='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
	            }
	        }
		}

        print '</div>'."\n";

        //Select mail models is same action as presend
		if (GETPOST('modelselected')) {
			$action = 'presend';
		}
		if ($action == 'presend')
		{
			/*
			 * Affiche formulaire mail
			*/

			// By default if $action=='presend'
			$titreform='SendMail';
			$topicmail='';
			$action='send';
			$modelmail='thirdparty';

    		print '<div id="formmailbeforetitle" name="formmailbeforetitle"></div>';
    		print '<div class="clearboth"></div>';
    		print '<br>';
			print load_fiche_titre($langs->trans($titreform));

			dol_fiche_head();

			// Define output language
			$outputlangs = $langs;
			$newlang = '';

			// Cree l'objet formulaire mail
			include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
			$formmail = new FormMail($db);
			$formmail->param['langsmodels']=(empty($newlang)?$langs->defaultlang:$newlang);
            $formmail->fromtype = (GETPOST('fromtype')?GETPOST('fromtype'):(!empty($conf->global->MAIN_MAIL_DEFAULT_FROMTYPE)?$conf->global->MAIN_MAIL_DEFAULT_FROMTYPE:'user'));

            if($formmail->fromtype === 'user'){
                $formmail->fromid = $user->id;

            }
			$formmail->trackid='thi'.$object->id;
			if (! empty($conf->global->MAIN_EMAIL_ADD_TRACK_ID) && ($conf->global->MAIN_EMAIL_ADD_TRACK_ID & 2))	// If bit 2 is set
			{
				include DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
				$formmail->frommail=dolAddEmailTrackId($formmail->frommail, 'thi'.$object->id);
			}
			$formmail->withfrom=1;
			$formmail->withtopic=1;
			
			$formmail->withto=$object->email;
			$formmail->withtofree=1;
			$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
			$formmail->withfile=2;
			$formmail->withbody=1;
			$formmail->withdeliveryreceipt=1;
			$formmail->withcancel=1;
			// Tableau des substitutions
			//$formmail->setSubstitFromObject($object);
			$formmail->substit['__THIRDPARTY_NAME__']=$object->name;
			$formmail->substit['__SIGNATURE__']=$user->signature;
			$formmail->substit['__PERSONALIZED__']='';
			$formmail->substit['__CONTACTCIVNAME__']='';

			// Tableau des parametres complementaires du post
			$formmail->param['action']=$action;
			$formmail->param['models']=$modelmail;
			$formmail->param['models_id']=GETPOST('modelmailselected','int');
			$formmail->param['socid']=$object->id;
			$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?socid='.$object->id;

			// Init list of files
			if (GETPOST("mode")=='init')
			{
				$formmail->clear_attached_files();
				$formmail->add_attached_files($file,basename($file),dol_mimetype($file));
			}
			print $formmail->get_form();

			dol_fiche_end();
		}
		else
		{


	        print '<div class="fichecenter"><br></div>';
		}
    }
}


// End of page
llxFooter();
$db->close();
