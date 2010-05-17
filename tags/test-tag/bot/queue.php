<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if(!array_key_exists('id', $_GET)) die("No id.");
	if(!array_key_exists('v', $_GET)) die("No v.");
	if(!array_key_exists('a', $_GET)) die("No a.");
	if(!array_key_exists('gid', $_GET)){
		$gid = 0;
	}else{
		$gid = $_GET['gid'];
	}

	$village = $_GET['v'];
	$id      = $_GET['id'];
	$account = intval($_GET['a']);
	
	require_once("db.php");
	
    $sql = "insert into build(account, id, village, gid) values($account, $id, $village, $gid)";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	header("Location: http://$host$uri/village.php?a=$account&id=$village");

?>
