<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	require_once('db.php');
	
	$sql = "select id, server, user from accounts order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	while($row = mysql_fetch_row($res)){
		$id = $row[0];
		$server = $row[1];
		$user = $row[2];
		
		echo " ----- $user @ $server -----\n";
		
		$sql = "update accounts set busy = 1 where id = $id";
		mysql_query($sql);
		
		$output = array();
		exec("/usr/local/bin/php cron_one.php $id", $output);
		foreach ($output as $line)
			echo "$line\n";

		$sql = "update accounts set busy = 0 where id = $id";
		mysql_query($sql);
	}
	
?>
