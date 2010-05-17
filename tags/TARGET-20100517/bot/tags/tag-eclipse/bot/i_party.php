<?php
	
	// ----------------------------------------------------------------------------
	// Main program
	// ----------------------------------------------------------------------------

	require_once("db.php");

	if(!array_key_exists('a', $_GET) || !array_key_exists('v', $_GET)) die("No post.");

	$a = intval($_GET['a']);
	$v  = intval($_GET['v']);
	
   	$sql = " update villages set party = party XOR 1 where account = $a and id = $v";
    if(!mysql_query($sql)) die(mysql_error());

	$referer = $_SERVER['HTTP_REFERER'];
	header("Location: $referer");

?>
