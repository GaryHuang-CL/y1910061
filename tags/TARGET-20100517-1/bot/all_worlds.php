<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	require_once('db.php');
	
	$sql = "select name from y1910061_travian.servers";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	while($row = mysql_fetch_row($res)){
		$name = $row[0];
		
		echo " ----- $name -----\n";

		$output = array();
		exec("/usr/local/bin/php world.php $name", $output);
		foreach ($output as $line)
			echo "$line\n";

	}
	
?>
