<?php
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhp/class/eabuilddepts.class.php';

top_httphead();
header("Content-Type: text/html");

$city = $_GET['city']?$_GET['city']:'';
$cityname = $_GET['cityname']?$_GET['cityname']:'';
$builddept = new Eabuilddepts($db);
if (($city != '' && $builddept->fetchByCity($city)>=0) || ($cityname != '' && $builddept->fetchByCityName($cityname)>=0))
{
	echo $builddept->getInfoDiv();
}
else
{
	echo $builddept->error;
}