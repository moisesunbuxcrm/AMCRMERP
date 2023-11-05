<?php
/**
 *	Start page for A&M Estimates tool
 */

// Load Dolibarr environment
@include("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

if (! $user->rights->amhpestimatesv2->estimates->read) accessforbidden();

$langs->load("amhpestimatesv2@amhpestimatesv2");

$form = new Form($db);
$formfile = new FormFile($db);

//
// Insert React App
//
// We should really cache the result of this process and only repeat if the date of the source html changes
//

// Load React app source HTML
$reactappHTML = file_get_contents('dev/build/index.html');

// Extract CSS references and insert into PHP file
$pattern = "/<link.*?href=\"\/AMCRMERP\/htdocs(.*?)\".*?>/i";
if(preg_match_all($pattern, $reactappHTML, $matches)) {
  $css = $matches[1];
}
llxHeader("",$langs->trans("AHMPEstimatesArea"), '', '', 0, 0, '', $css);

// Remove other unnecessary HTML source
$reactappHTML = preg_replace('/<!doctype html>/', '', $reactappHTML);
$reactappHTML = preg_replace('/<html lang="en">/', '', $reactappHTML);
$reactappHTML = preg_replace('/<head>.*?<\/head>/', '', $reactappHTML);
$reactappHTML = preg_replace('/<body>/', '', $reactappHTML);
$reactappHTML = preg_replace('/<\/body><\/html>/', '', $reactappHTML);

// Insert into PHP
print $reactappHTML;

llxFooter();

$db->close();
