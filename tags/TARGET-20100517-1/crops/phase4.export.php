<?php

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if($argc < 2){
		die("server id needed.");
	}
	
	$serverid = intval($argv[1]);
	
	require_once('common.php');
	require_once('db.php');
	
	$sql = "replace into crops(x, y, d, c, crops, bonus, server) select x, y, d, c, crops, bonus, $serverid from crop_crawler where crops > 6";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());

?>
