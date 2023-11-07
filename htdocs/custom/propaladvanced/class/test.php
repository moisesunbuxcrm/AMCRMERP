<?php

global $conf;
// Define DOL_DOCUMENT_ROOT used for install/upgrade process
if (!defined('DOL_DOCUMENT_ROOT')) {
	define('DOL_DOCUMENT_ROOT', 'C:/xampp/htdocs/AMCRMERP/htdocs');
}

require_once DOL_DOCUMENT_ROOT.'/custom/propaladvanced/class/getData.php';

$colors = getColors();

print_r($colors);
