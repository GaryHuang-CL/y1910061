<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once("db.php");

	if(!array_key_exists('a', $_GET) || !array_key_exists('v', $_GET) || !array_key_exists('ac', $_GET)) die("No post.");

	$a  = intval($_GET['a']);
	$ac = intval($_GET['ac']);
	$v  = intval($_GET['v']);
	
   	$sql = " update villages set auto_transfer = $a where account = $ac and id = $v";
    if(!mysql_query($sql)) die(mysql_error());

	$referer = $_SERVER['HTTP_REFERER'];
	header("Location: $referer");

?>