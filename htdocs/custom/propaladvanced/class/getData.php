<?php

function conexion(){
	$host = 'localhost';
	$dbname = 'hurricane2';
	$username = 'root';
	$password = '';

	try {
		$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
		return $conn;
	} catch (PDOException $pe) {
		die("Could not connect to the database $dbname :" . $pe->getMessage());
	}
}
function getColors(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_colors lec order by name  ");
	$colors_array = [];
	if($res){
		$colors = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($colors as $color){
			$id = $color['id'];
			$name = $color['name'];
			$colors_array[$id]=$name;
		}
	}

	return $colors_array;

}

function getProductsTypes(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_producttypes lep order by name");
	$products_array = [];
	if($res){
		$products = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($products as $product){
			$id = $product['id'];
			$name = $product['name'];
			$products_array[$id]=$name;
		}
	}

	return $products_array;

}

function getMaterialsTypes(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_materials lem order by name");
	$materials_array = [];
	if($res){
		$materials = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($materials as $material){
			$id = $material['name'];
			$name = $material['name'];
			$materials_array[$id]=$name;
		}
	}

	return $materials_array;

}

function getWindowsTypes(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_windowtypes lew order by name");
	$windows_array = [];
	if($res){
		$windows = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($windows as $window){
			$id = $window['name'];
			$name = $window['name'];
			$windows_array[$id]=$name;
		}
	}

	return $windows_array;

}

function getItemsTypes(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_itemtypes lei  order by name");
	$items_array = [];
	if($res){
		$items = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($items as $item){
			$id = $item['name'];
			$name = $item['name'];
			$items_array[$id]=$name;
		}
	}

	return $items_array;

}

function getMount(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_mounts lem  order by name");
	$mounts_array = [];
	if($res){
		$mounts = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($mounts as $mount){
			$id = $mount['name'];
			$name = $mount['name'];
			$mounts_array[$id]=$name;
		}
	}

	return $mounts_array;

}

function getAngularType(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_angulartypes lea  order by name");
	$angular_array = [];
	if($res){
		$angular = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($angular as $item){
			$id = $item['name'];
			$name = $item['name'];
			$angular_array[$id]=$name;
		}
	}

	return $angular_array;

}

function getLock(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_lockins lel order by name");
	$lock_array = [];
	if($res){
		$lock = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($lock as $item){
			$id = $item['name'];
			$name = $item['name'];
			$lock_array[$id]=$name;
		}
	}

	return $lock_array;

}

function getLockSizes(){

	$conn = conexion();
	$res = $conn->query("select * from llx_ea_locksizes lel order by size");
	$locksizes_array = [];
	if($res){
		$locksizes = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach ($locksizes as $item){
			$id = $item['size'];
			$name = $item['size'];
			$locksizes_array[$id]=$name;
		}
	}

	return $locksizes_array;

}

print_r(getProductsTypes());


