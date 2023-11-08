<?php

/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       propaladvanced/propaladvancedindex.php
 *	\ingroup    propaladvanced
 *	\brief      Home page of propaladvanced top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

function getColors(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_colors lec order by name  ";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->id;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getProductsTypes(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_producttypes lep order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->id;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getMaterialsTypes(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_materials lem order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getWindowsTypes(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_windowtypes lew order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getItemsTypes(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_itemtypes lei  order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getMount(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_mounts lem  order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getAngularType(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_angulartypes lea  order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getLock(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_lockins lel order by name";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->name;
			$name = $obj->name;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}

function getLockSizes(){

	global $db;
	$query = "select * from ". MAIN_DB_PREFIX ."ea_locksizes lel order by size";
	$resql = $db->query($query);
	$array = [];

	if($resql){
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num){
			$obj = $db->fetch_object($resql);
			$id= $obj->size;
			$name = $obj->size;
			$array[$id]=$name;
			$i++;
		}
	}
	return $array;
}
