<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if(!array_key_exists('id', $_GET)) die("No id.");
	if(!array_key_exists('v', $_GET)) die("No v.");
	if(!array_key_exists('a', $_GET)) die("No a.");

	$village = $_GET['v'];
	$id      = $_GET['id'];
	$account = intval($_GET['a']);
	
	require_once("db.php");
	
	$sql = "select gid, name from gids";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());

	while($row = mysql_fetch_row($res)){
		$gid  = $row[0];
		$name = $row[1];
		
		
		echo "<a href=\"queue.php?a=$account&v=$village&id=$id&gid=$gid\">$name</a><br>\n";
	}
?>
