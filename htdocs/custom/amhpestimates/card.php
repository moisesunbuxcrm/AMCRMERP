<?php
/**
 *	Start page for A&M Estimates tool
 */

// Load Dolibarr environment
@include("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

if (! $user->rights->amhp->estimates->read) accessforbidden();

$langs->load("amhpestimates@amhpestimates");

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("",$langs->trans("AHMPEstimatesArea"));

print '<div id="estimates-app"></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';
print '</div></div></div>';
print '<script src="formdata.js.php"></script>';
print '<script src="estimates.js"></script>';

llxFooter();

$db->close();
