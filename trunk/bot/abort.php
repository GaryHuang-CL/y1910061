<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	
	require_once("db.php");
	
	if(!array_key_exists('seq', $_GET)) die("No seq.");
	if(!array_key_exists('a', $_GET)) die("No a.");

	$seq     = $_GET['seq'];
	$account = intval($_GET['a']);
	
	$sql = " select village from build where account = $account and `seq` = $seq ";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    $row = mysql_fetch_row($res);
    if(!$row) die("No seq found.");
    $village = $row[0];
    
    
	$sql = " delete from build where account = $account and `seq` = $seq ";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());

	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	header("Location: http://$host$uri/village.php?a=$account&id=$village");
?>
