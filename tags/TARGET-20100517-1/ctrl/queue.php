<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if(!array_key_exists('id', $_GET)) die("No id.");
	if(!array_key_exists('v', $_GET)) die("No v.");

	$village = $_GET['v'];
	$id = $_GET['id'];
	
	require_once("db.php");
	
    $sql = "insert into build(id, village) values($id, $village)";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	header("Location: http://$host$uri/village.php?id=$village");

?>
