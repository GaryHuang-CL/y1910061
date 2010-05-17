<?php
	
	// ----------------------------------------------------------------------------
	// Main program
	// ----------------------------------------------------------------------------

	require_once("db.php");

	if(!array_key_exists('x', $_GET) || !array_key_exists('y', $_GET) || !array_key_exists('t', $_GET)) die("No post.");

	$x = $_GET['x'];
	$y = $_GET['y'];
	$t = $_GET['t'];
	
	if(array_key_exists('i', $_GET)){
		$interval = max(1, intval($_GET['i']));
	}else{
		$interval = 1;
	}

	if(array_key_exists('r', $_GET)){
		$raid = max(1, intval($_GET['r']));
	}else{
		$raid = 1;
	}
	
	$sql = "select id, sqrt(abs($x - x) * abs($x - x) + abs($y - y) * abs($y - y)) as distance from villages where noraid = 0 order by distance limit 1";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	$village = $row[0];
	
   	$sql = " replace into targets(x, y, invalid, village, `interval`, `raid`) values ($x, $y, $t, $village, $interval, $raid)";
    if(!mysql_query($sql)) die(mysql_error());

	$referer = $_SERVER['HTTP_REFERER'];
	header("Location: $referer");

?>